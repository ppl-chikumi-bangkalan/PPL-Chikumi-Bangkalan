<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function categories()
    {
        return response()->json([
            'data' => collect(Expense::CATEGORIES)
                ->map(fn (string $label, string $kode) => ['kode' => $kode, 'label' => $label])
                ->values(),
            'metode' => collect(Expense::PAYMENT_METHODS)
                ->map(fn (string $label, string $kode) => ['kode' => $kode, 'label' => $label])
                ->values(),
        ]);
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'periode' => ['nullable', 'date_format:Y-m'],
            'kategori' => ['nullable', Rule::in(array_keys(Expense::CATEGORIES))],
            'per_halaman' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $base = Expense::query()
            ->when($data['periode'] ?? null, function ($query, string $periode) {
                [$tahun, $bulan] = explode('-', $periode);
                $query->whereYear('expense_date', $tahun)->whereMonth('expense_date', $bulan);
            })
            ->when($data['kategori'] ?? null, fn ($query, $kategori) => $query->where('category', $kategori));

        $ringkasan = [
            'total' => (float) (clone $base)->sum('amount'),
            'jumlah' => (clone $base)->count(),
        ];

        $halaman = $base->with('pencatat:id,name')
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->paginate($data['per_halaman'] ?? 20);

        return response()->json([
            'data' => [
                'pengeluaran' => collect($halaman->items())
                    ->map(fn (Expense $expense) => $this->bentuk($expense))
                    ->values(),
                'ringkasan' => $ringkasan,
                'meta' => [
                    'halaman' => $halaman->currentPage(),
                    'per_halaman' => $halaman->perPage(),
                    'total' => $halaman->total(),
                    'total_halaman' => $halaman->lastPage(),
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateExpense($request);
        $data['payment_method'] ??= 'cash';
        $data['created_by'] = $request->user()?->id;

        $expense = Expense::create($data);

        return response()->json([
            'message' => 'Pengeluaran berhasil dicatat.',
            'data' => $this->bentuk($expense->load('pencatat:id,name')),
        ], 201);
    }

    public function update(Request $request, Expense $pengeluaran)
    {
        $pengeluaran->update($this->validateExpense($request));

        return response()->json([
            'message' => 'Pengeluaran berhasil diperbarui.',
            'data' => $this->bentuk($pengeluaran->fresh('pencatat:id,name')),
        ]);
    }

    public function destroy(Expense $pengeluaran)
    {
        $pengeluaran->delete();

        return response()->json(['message' => 'Pengeluaran berhasil dihapus.']);
    }

    private function validateExpense(Request $request): array
    {
        return $request->validate([
            'expense_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:1'],
            'category' => ['required', Rule::in(array_keys(Expense::CATEGORIES))],
            'payment_method' => ['nullable', Rule::in(array_keys(Expense::PAYMENT_METHODS))],
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function bentuk(Expense $expense): array
    {
        return [
            'id' => $expense->id,
            'tanggal' => $expense->expense_date?->format('Y-m-d'),
            'judul' => $expense->title,
            'kategori' => $expense->category,
            'kategori_label' => Expense::CATEGORIES[$expense->category] ?? $expense->category,
            'metode' => $expense->payment_method,
            'metode_label' => Expense::PAYMENT_METHODS[$expense->payment_method] ?? $expense->payment_method,
            'nominal' => (float) $expense->amount,
            'catatan' => $expense->notes,
            'dicatat_oleh' => $expense->pencatat?->name,
            'dibuat_pada' => $expense->created_at?->toDateTimeString(),
        ];
    }
}
