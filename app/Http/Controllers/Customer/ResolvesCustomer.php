<?php

namespace App\Http\Controllers\Customer;

use App\Models\User;
use Illuminate\Http\Request;

trait ResolvesCustomer
{
    // Dev route (tanpa login) mengirim `X-Dev-Customer: <id_user>`;
    // route resmi selalu pakai session login.
    protected function customerId(Request $request): int
    {
        if ($id = $request->user()?->id) {
            return $id;
        }
        $user = User::where('id', $request->header('X-Dev-Customer'))
            ->where('role', 'customer')
            ->first();
        abort_unless($user, 403, 'Login pelanggan diperlukan (dev: kirim header X-Dev-Customer).');

        return $user->id;
    }
}
