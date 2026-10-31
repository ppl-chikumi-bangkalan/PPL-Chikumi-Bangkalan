<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $login = trim($data['email']);
        $remember = $request->boolean('remember');

        if (! Auth::attempt(['email' => $login, 'password' => $data['password']], $remember)) {
            if (! Auth::attempt(['phone_number' => $login, 'password' => $data['password']], $remember)) {
                return back()
                    ->withErrors(['email' => 'Email/No. WhatsApp atau password salah.'])
                    ->onlyInput('email');
            }
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectFor(Auth::user()->role));
    }

    protected function redirectFor(string $role): string
    {
        return match ($role) {
            'owner' => route('owner.dashboard'),
            'cashier' => route('kasir.dashboard'),
            default => route('customer.dashboard'),
        };
    }
}
