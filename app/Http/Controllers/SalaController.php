<?php

namespace App\Http\Controllers;

use App\Enums\EstadoUtilizador;
use App\Models\Sala;
use App\Models\Utilizador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class SalaController extends Controller
{
    public function index(Request $request): View
    {
        $salas = $request->user()
            ->salas()
            ->with('criador')
            ->withCount('utilizadores')
            ->latest()
            ->get();

        return view('salas.index', [
            'salas' => $salas,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Sala::class);

        return view('salas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Sala::class);

        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $caminhoAvatar = null;

        if ($request->hasFile('avatar')) {
            $caminhoAvatar = $request->file('avatar')->store('salas', 'public');
        }

        try {
            DB::transaction(function () use ($request, $dados, $caminhoAvatar) {
                $sala = $request->user()->salasCriadas()->create([
                    'nome' => trim($dados['nome']),
                    'avatar' => $caminhoAvatar,
                ]);

                $sala->utilizadores()->attach($request->user()->id);
            });
        } catch (Throwable $e) {
            if ($caminhoAvatar && Storage::disk('public')->exists($caminhoAvatar)) {
                Storage::disk('public')->delete($caminhoAvatar);
            }

            throw $e;
        }

        return redirect()->route('salas.index')->with('sucesso', 'Sala criada com sucesso.');
    }

    public function show(Request $request, Sala $sala): View
    {
        Gate::authorize('view', $sala);

        $sala->load([
            'criador',
            'utilizadores' => fn ($query) => $query->orderBy('nome'),
            'mensagens' => fn ($query) => $query->with('utilizador')->cronologico(),
        ]);

        $utilizadoresDisponiveis = collect();

        if ($request->user()->isAdmin()) {
            $membrosIds = $sala->utilizadores->pluck('id');
            $utilizadoresDisponiveis = Utilizador::where('estado', EstadoUtilizador::ATIVO)
                ->whereNotIn('id', $membrosIds)
                ->orderBy('nome')
                ->get();
        }

        return view('salas.show', [
            'sala' => $sala,
            'mensagens' => $sala->mensagens,
            'utilizadoresDisponiveis' => $utilizadoresDisponiveis,
        ]);
    }
}