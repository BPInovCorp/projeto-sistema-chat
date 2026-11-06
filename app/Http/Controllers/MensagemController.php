<?php

namespace App\Http\Controllers;

use App\Models\Sala;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MensagemController extends Controller
{
    public function storeSala(Request $request, Sala $sala): RedirectResponse
    {
        Gate::authorize('sendMessage', $sala);

        $request->merge([
            'conteudo' => is_string($request->input('conteudo')) ? trim($request->input('conteudo')) : $request->input('conteudo'),
        ]);

        $dados = $request->validate([
            'conteudo' => ['required', 'string', 'max:5000'],
        ]);

        $sala->mensagens()->create([
            'utilizador_id' => $request->user()->id,
            'conteudo' => $dados['conteudo'],
        ]);

        return back();
    }
}