<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\ProductSize;
use App\Models\StockHistory;
use App\Models\User;
use App\Support\PriceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_method' => ['required', 'in:cash,non_cash'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_size_id' => ['required', 'integer', 'exists:product_sizes,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.variant_ids' => ['nullable', 'array'],
            'items.*.variant_ids.*' => ['integer', 'distinct'],
        ]);

        $cashierId = $this->cashierId($request);

        $transaction = DB::transaction(function () use ($data, $cashierId) {
            // Kelompokkan per ukuran agar dua baris dengan ukuran sama tidak menjebol stok.
            // Urutkan id -> hindari deadlock saat dua kasir checkout bersamaan.
            $groups = collect($data['items'])->groupBy('product_size_id')->sortKeys();

            $total = 0.0;
            $prepared = [];
            $stockOut = [];
            foreach ($groups as $sizeId => $rows) {
                $size = ProductSize::with('product:id,name,is_active')
                    ->lockForUpdate()
                    ->findOrFail($sizeId);
                abort_unless($size->product->is_active, 422, 'Produk tidak tersedia.');

                $quantity = (int) $rows->sum('quantity');
                abort_unless($size->stock >= $quantity, 422, "Stok {$size->size_name} tidak cukup — sisa {$size->stock}.");

                foreach ($rows as $row) {
                    $variants = PriceCalculator::resolveVariants($size->product_id, $row['variant_ids'] ?? []);
                    $price = PriceCalculator::breakdown($size, $variants, $row['quantity']);
                    $prepared[] = ['size' => $size, 'variants' => $variants, 'price' => $price];
                    $total += $price['subtotal'];
                }

                $stockOut[] = ['size' => $size, 'quantity' => $quantity];
            }

            $paid = (float) $data['amount_paid'];

            if ($data['payment_method'] === 'non_cash') {
                abort_if($paid < $total, 422, 'Nominal non-tunai kurang dari total.');
                $paid = $total; // non-tunai tidak ada kembalian
            }

            abort_if($paid < $total, 422, 'Uang dibayar kurang dari total.');

            $transaction = PosTransaction::create([
                'transaction_number' => 'POS-'.now()->format('ymdHis').'-'.random_int(1000, 9999),
                'cashier_id' => $cashierId,
                'customer_name' => $data['customer_name'] ?? null,
                'total_amount' => round($total, 2),
                'payment_method' => $data['payment_method'],
                'amount_paid' => round($paid, 2),
                'change_amount' => round($paid - $total, 2),
                'status' => 'paid',
            ]);

            foreach ($prepared as $row) {
                $size = $row['size'];
                $price = $row['price'];
                $item = PosTransactionItem::create([
                    'pos_transaction_id' => $transaction->id,
                    'product_size_id' => $size->id,
                    'unit_price' => $price['harga_satuan'],
                    'quantity' => $price['quantity'],
                    'subtotal' => $price['subtotal'],
                ]);
                foreach ($row['variants'] as $variant) {
                    $item->variants()->attach($variant->id, ['variant_price' => $variant->additional_price]);
                }
            }

            foreach ($stockOut as $out) {
                $out['size']->update(['stock' => $out['size']->stock - $out['quantity']]);
                StockHistory::create([
                    'product_size_id' => $out['size']->id,
                    'user_id' => $cashierId,
                    'type' => 'out',
                    'quantity' => $out['quantity'],
                    'source' => 'pos_transaction',
                    'reference_id' => $transaction->id,
                ]);
            }

            return $transaction;
        });

        return response()->json([
            'message' => 'Transaksi berhasil disimpan.',
            'data' => $this->payload($transaction->fresh()),
        ], 201);
    }

    private function payload(PosTransaction $transaction): array
    {
        $transaction->load(['items.productSize.product:id,name,image_url', 'items.variants']);

        return [
            'id' => $transaction->id,
            'nomor' => $transaction->transaction_number,
            'pelanggan' => $transaction->customer_name,
            'kasir' => $transaction->cashier?->name,
            'metode_pembayaran' => $transaction->payment_method,
            'total' => (float) $transaction->total_amount,
            'dibayar' => (float) $transaction->amount_paid,
            'kembalian' => (float) $transaction->change_amount,
            'status' => $transaction->status,
            'tanggal' => $transaction->created_at->format('Y-m-d H:i'),
            'item' => $transaction->items->map(fn (PosTransactionItem $item) => [
                'produk' => $item->productSize->product->name,
                'gambar' => $item->productSize->product->image_url,
                'ukuran' => $item->productSize->size_name,
                'varian' => $item->variants->map(fn ($variant) => [
                    'id' => $variant->id,
                    'type' => $variant->type,
                    'nama' => $variant->name,
                    'tambahan_harga' => (float) $variant->pivot->variant_price,
                ])->values(),
                'harga_satuan' => (float) $item->unit_price,
                'quantity' => $item->quantity,
                'subtotal' => (float) $item->subtotal,
            ])->values(),
        ];
    }

    // Dev route (tanpa login) mengirim `X-Dev-Kasir: <id_user>`; route resmi pakai session.
    private function cashierId(Request $request): int
    {
        if ($id = $request->user()?->id) {
            return $id;
        }
        $user = User::where('id', $request->header('X-Dev-Kasir'))
            ->whereIn('role', ['owner', 'cashier'])
            ->first();
        abort_unless($user, 403, 'Login kasir diperlukan (dev: kirim header X-Dev-Kasir).');

        return $user->id;
    }
}
