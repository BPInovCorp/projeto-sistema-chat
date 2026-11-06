<?php

namespace App\Http\Controllers;

use App\Models\Sala;
use App\Models\Utilizador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SalaMembroController extends Controller
{
    public function store(Request $request, Sala $sala): RedirectResponse
    {
        Gate::authorize('manageMembers', $sala);

        $dados = $request->validate([
            'utilizador_id' => ['required', 'exists:utilizadores,id'],
        ]);

        $utilizador = Utilizador::findOrFail($dados['utilizador_id']);

        if (! $utilizador->isAtivo()) {
            return back()->with('erro', 'Não é possível adicionar colaboradores com conta inativa.');
        }

        $sala->utilizadores()->syncWithoutDetaching([$utilizador->id]);

        return back()->with('sucesso', 'Colaborador adicionado à sala com sucesso.');
    }

    public function destroy(Sala $sala, Utilizador $utilizador): RedirectResponse
    {
        Gate::authorize('manageMembers', $sala);

        if ($sala->utilizador_id === $utilizador->id) {
            return back()->with('erro', 'Não é possível remover o criador da sala.');
        }

        if (! $sala->temMembro($utilizador->id)) {
            return back()->with('erro', 'O colaborador não pertence a esta sala.');
        }

        $sala->utilizadores()->detach($utilizador->id);

        return back()->with('sucesso', 'Colaborador removido da sala com sucesso.');
    }
}