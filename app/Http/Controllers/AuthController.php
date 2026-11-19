<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'nim' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'max:30',
                'unique:users,nim',
            ],
            'email' => ['required', 'email', 'max:254', 'unique:users,email'],
            'program_studi' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'max:128'],
        ]);

        User::create($data);

        return redirect()
            ->route('login')
            ->with('status', 'Akun berhasil dibuat. Silakan masuk.');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'identity' => ['required', 'string', 'max:254'],
            'password' => ['required', 'string', 'max:128'],
        ]);

        $identity = trim($data['identity']);
        $field = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'nim';

        if ($field === 'email') {
            $identity = strtolower($identity);
        }

        if (! Auth::attempt([
            $field => $identity,
            'password' => $data['password'],
        ])) {
            throw ValidationException::withMessages([
                'identity' => 'Email/NIM atau kata sandi tidak sesuai.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'Berhasil keluar.');
    }
}