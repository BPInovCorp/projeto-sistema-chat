<?php

namespace App\Http\Controllers;

use App\Enums\EstadoUtilizador;
use App\Models\Utilizador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credenciais = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $utilizador = Utilizador::where('email', $credenciais['email'])->first();

        if ($utilizador && ! $utilizador->isAtivo()) {
            return back()->withErrors([
                'email' => 'A sua conta encontra-se desativada. Contacte a administração.',
            ])->onlyInput('email');
        }

        if (! Auth::attempt([
            'email' => $credenciais['email'],
            'password' => $credenciais['password'],
            'estado' => EstadoUtilizador::ATIVO->value,
        ], $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'As credenciais indicadas não estão corretas.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}