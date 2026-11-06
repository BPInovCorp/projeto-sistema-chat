@extends('layouts.app')

@section('titulo', 'Salas de Conversação — Inovcorp Chat')

@section('conteudo')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight">Salas de Conversação</h1>
            <p class="text-xs text-base-content/60 mt-0.5">Canais de equipa aos quais tem acesso</p>
        </div>

        @can('create', App\Models\Sala::class)
            <div>
                <a href="{{ route('salas.create') }}" class="btn btn-neutral btn-sm gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Criar nova sala</span>
                </a>
            </div>
        @endcan
    </div>

    @if ($salas->isEmpty())
        <div class="card bg-base-100 border border-base-300 shadow-sm p-8 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-base-200 text-base-content/60 mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                </svg>
            </div>
            <h2 class="text-sm font-semibold">Sem salas associadas</h2>
            <p class="text-xs text-base-content/60 mt-1 max-w-sm mx-auto">
                Ainda não pertence a nenhuma sala de conversação. Aguarde que um administrador o associe a um canal.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach ($salas as $sala)
                <div class="card bg-base-100 border border-base-300 shadow-sm hover:border-neutral/30 transition-colors">
                    <div class="card-body p-5 flex flex-col justify-between">
                        <div class="flex items-start gap-3">
                            @if ($sala->avatar)
                                <img src="{{ asset('storage/' . $sala->avatar) }}" alt="{{ $sala->nome }}" class="w-10 h-10 rounded-lg object-cover shrink-0" />
                            @else
                                <div class="avatar placeholder shrink-0">
                                    <div class="bg-neutral text-neutral-content rounded-lg w-10 h-10 font-bold text-xs flex items-center justify-center">
                                        <span>{{ mb_strtoupper(mb_substr($sala->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                                    </div>
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <h2 class="text-sm font-bold truncate">{{ $sala->nome }}</h2>
                                <p class="text-[11px] text-base-content/60 truncate">
                                    Criada por {{ $sala->criador->nome }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 mt-2 border-t border-base-200">
                            <span class="text-xs text-base-content/60 font-medium">
                                {{ $sala->utilizadores_count ?? $sala->utilizadores->count() }} {{ ($sala->utilizadores_count ?? $sala->utilizadores->count()) === 1 ? 'membro' : 'membros' }}
                            </span>

                            <a href="{{ route('salas.show', $sala) }}" class="btn btn-ghost btn-xs font-semibold hover:bg-neutral hover:text-neutral-content">
                                Entrar
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection