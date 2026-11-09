<?php

namespace App\Http\Controllers;

use App\Enums\EstadoUtilizador;
use App\Models\ConversaDireta;
use App\Models\Utilizador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ConversaDiretaController extends Controller
{
    public function index(Request $request): View
    {
        $colegas = Utilizador::where('estado', EstadoUtilizador::ATIVO)
            ->where('id', '!=', $request->user()->id)
            ->orderBy('nome')
            ->get();

        $conversas = ConversaDireta::where('utilizador_um_id', $request->user()->id)
            ->orWhere('utilizador_dois_id', $request->user()->id)
            ->with(['primeiroUtilizador', 'segundoUtilizador'])
            ->latest('updated_at')
            ->get();

        return view('conversas-diretas.index', [
            'colegas' => $colegas,
            'conversas' => $conversas,
        ]);
    }

    public function show(Request $request, Utilizador $utilizador): View
    {
        if ($request->user()->id === $utilizador->id) {
            abort(400, 'Não é possível iniciar uma conversa consigo próprio.');
        }

        if (! $utilizador->isAtivo()) {
            abort(404, 'O colaborador indicado não se encontra ativo.');
        }

        $conversa = ConversaDireta::encontrarOuCriar($request->user()->id, $utilizador->id);

        Gate::authorize('view', $conversa);

        $parceiro = $conversa->outroUtilizador($request->user()->id);

        $mensagens = $conversa->mensagens()
            ->with('utilizador')
            ->cronologico()
            ->get();

        return view('conversas-diretas.show', [
            'conversa' => $conversa,
            'parceiro' => $parceiro,
            'mensagens' => $mensagens,
        ]);
    }
}