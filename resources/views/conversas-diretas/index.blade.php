@extends('layouts.app')

@section('titulo', 'Mensagens Diretas — Inovcorp Chat')

@section('conteudo')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight">Mensagens Diretas</h1>
            <p class="text-xs text-base-content/60 mt-0.5">Conversações privadas e diretas entre membros da equipa</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('salas.index') }}" class="btn btn-ghost btn-sm text-xs font-medium">
                Ver Salas
            </a>
        </div>
    </div>

    <div class="space-y-8">
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold uppercase tracking-wider text-base-content/70">Membros da Equipa</h2>
                <span class="text-xs text-base-content/50">{{ $colegas->count() }} disponíveis</span>
            </div>

            @if ($colegas->isEmpty())
                <div class="card bg-base-100 border border-base-300 p-6 text-center shadow-sm">
                    <p class="text-xs text-base-content/60">Não existem outros colaboradores ativos de momento.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach ($colegas as $colega)
                        <div class="card bg-base-100 border border-base-300 p-4 shadow-sm flex flex-col justify-between">
                            <div class="flex items-center gap-3">
                                <div class="avatar {{ $colega->avatar ? '' : 'placeholder' }} shrink-0">
                                    @if ($colega->avatar)
                                        <div class="w-10 h-10 rounded-full">
                                            <img src="{{ asset('storage/' . $colega->avatar) }}" alt="{{ $colega->nome }}" class="object-cover" />
                                        </div>
                                    @else
                                        <div class="bg-neutral text-neutral-content rounded-full w-10 h-10 font-bold text-xs flex items-center justify-center">
                                            <span>{{ mb_strtoupper(mb_substr($colega->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h3 class="text-xs font-bold truncate">{{ $colega->nome }}</h3>
                                    <p class="text-[11px] text-base-content/60 truncate">{{ $colega->email }}</p>
                                    <span class="badge badge-ghost badge-xs text-[9px] mt-1 font-medium">
                                        {{ $colega->permissao->label() }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-t border-base-200 flex justify-end">
                                <a href="{{ route('conversas-diretas.show', $colega) }}" class="btn btn-neutral btn-xs font-medium">
                                    Conversar
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold uppercase tracking-wider text-base-content/70">Conversas Ativas</h2>
            </div>

            @if ($conversas->isEmpty())
                <div class="card bg-base-100 border border-base-300 p-6 text-center shadow-sm">
                    <div class="w-10 h-10 rounded-full bg-base-200 flex items-center justify-center text-base-content/50 mx-auto mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <p class="text-xs text-base-content/60">Ainda não tem conversas diretas ativas. Escolha um colega acima para começar.</p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($conversas as $conversa)
                        @php
                            $parceiro = $conversa->outroUtilizador(auth()->id());
                        @endphp
                        @if ($parceiro)
                            <div class="card bg-base-100 border border-base-300 p-4 shadow-sm hover:border-neutral/30 transition-colors flex flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="avatar {{ $parceiro->avatar ? '' : 'placeholder' }} shrink-0">
                                        @if ($parceiro->avatar)
                                            <div class="w-10 h-10 rounded-full">
                                                <img src="{{ asset('storage/' . $parceiro->avatar) }}" alt="{{ $parceiro->nome }}" class="object-cover" />
                                            </div>
                                        @else
                                            <div class="bg-neutral text-neutral-content rounded-full w-10 h-10 font-bold text-xs flex items-center justify-center">
                                                <span>{{ mb_strtoupper(mb_substr($parceiro->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold truncate">{{ $parceiro->nome }}</h3>
                                        <span class="text-[11px] text-base-content/50">
                                            Atividade: {{ $conversa->updated_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                </div>

                                <a href="{{ route('conversas-diretas.show', $parceiro) }}" class="btn btn-ghost btn-xs font-semibold hover:bg-neutral hover:text-neutral-content shrink-0">
                                    Abrir conversa
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection