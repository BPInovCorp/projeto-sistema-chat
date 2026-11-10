@extends('layouts.app')

@section('titulo', '#' . $sala->nome . ' — Inovcorp Chat')

@section('conteudo')
    <div class="flex gap-6 h-[calc(100vh-8.5rem)] w-full">
        <div class="flex-1 flex flex-col bg-base-100 border border-base-300 rounded-2xl shadow-sm overflow-hidden relative min-w-0">
            <header class="navbar bg-base-100 border-b border-base-300 px-4 py-2 shrink-0 z-10 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    @if ($sala->avatar)
                        <img src="{{ asset('storage/' . $sala->avatar) }}" alt="{{ $sala->nome }}" class="w-8 h-8 rounded-lg object-cover" />
                    @else
                        <div class="avatar placeholder">
                            <div class="bg-neutral text-neutral-content rounded-lg w-8 h-8 font-bold text-xs flex items-center justify-center">
                                <span>{{ mb_strtoupper(mb_substr($sala->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                            </div>
                        </div>
                    @endif

                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="font-bold text-sm leading-none">#{{ $sala->nome }}</h1>
                        </div>
                        <span class="text-[11px] text-base-content/60">
                            {{ $sala->utilizadores->count() }} {{ $sala->utilizadores->count() === 1 ? 'membro' : 'membros' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-1.5">
                    @can('update', $sala)
                        <a href="{{ route('salas.edit', $sala) }}" class="btn btn-ghost btn-sm btn-square text-base-content/70 hover:text-base-content" title="Editar sala" aria-label="Editar sala">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>
                    @endcan

                    <button type="button" onclick="modal_membros.showModal()" class="btn btn-ghost btn-sm gap-1.5 text-xs font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Membros</span>
                    </button>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4">
                @if ($mensagens->isEmpty())
                    <div class="h-full flex flex-col items-center justify-center text-center p-8">
                        <div class="w-12 h-12 rounded-full bg-base-200 flex items-center justify-center text-base-content/50 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold">Ainda não existem mensagens nesta sala</p>
                        <p class="text-xs text-base-content/60 mt-1">Seja o primeiro a iniciar a conversa com a equipa!</p>
                    </div>
                @else
                    @foreach ($mensagens as $mensagem)
                        @php
                            $souEu = $mensagem->utilizador_id === auth()->id();
                        @endphp
                        <div class="chat {{ $souEu ? 'chat-end' : 'chat-start' }}">
                            <div class="chat-image avatar {{ $mensagem->utilizador->avatar ? '' : 'placeholder' }}">
                                @if ($mensagem->utilizador->avatar)
                                    <div class="w-8 h-8 rounded-full overflow-hidden">
                                        <img src="{{ asset('storage/' . $mensagem->utilizador->avatar) }}" alt="{{ $mensagem->utilizador->nome }}" class="w-full h-full object-cover" />
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-full text-xs font-semibold flex items-center justify-center {{ $souEu ? 'bg-primary text-primary-content' : 'bg-neutral text-neutral-content' }}">
                                        <span>{{ mb_strtoupper(mb_substr($mensagem->utilizador->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="chat-header text-[11px] text-base-content/60 mb-1 flex items-center gap-1.5">
                                <span class="font-semibold text-base-content/80">{{ $mensagem->utilizador->nome }}</span>
                                <time class="text-[10px]">{{ $mensagem->created_at->format('d/m/Y H:i') }}</time>
                            </div>
                            <div class="chat-bubble text-sm leading-relaxed {{ $souEu ? 'chat-bubble-primary' : 'bg-base-200 text-base-content border border-base-300' }}">
                                {!! nl2br(e($mensagem->conteudo)) !!}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="p-3 md:p-4 bg-base-100 border-t border-base-200 shrink-0">
                <form method="POST" action="{{ route('salas.mensagens.store', $sala) }}" class="max-w-3xl mx-auto bg-base-200/50 border border-base-300 rounded-full shadow-sm flex items-center px-4 py-1.5 gap-2 focus-within:border-primary focus-within:bg-base-100 transition-all">
                    @csrf
                    <input 
                        type="text" 
                        name="conteudo" 
                        required 
                        autocomplete="off" 
                        placeholder="Escreva uma mensagem para #{{ $sala->nome }}..." 
                        class="input input-ghost input-sm focus:outline-none w-full text-sm px-1 border-none bg-transparent"
                    />
                    <button type="submit" class="btn btn-neutral btn-sm btn-circle shrink-0" aria-label="Enviar mensagem">
                        <img src="{{ asset('images/send-svgrepo-com.svg') }}" alt="Enviar mensagem" width="16" height="16" class="w-4 h-4 object-contain brightness-0 invert shrink-0" />
                    </button>
                </form>
            </div>
        </div>

        <aside class="hidden lg:flex flex-col w-64 bg-base-100 border border-base-300 rounded-2xl shadow-sm p-4 shrink-0 overflow-hidden select-none">
            <div class="mb-4 pb-3 border-b border-base-200">
                <div class="flex items-center justify-between mb-2 px-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/50">Pings</span>
                    <a href="{{ route('conversas-diretas.index') }}" class="btn btn-ghost btn-xs text-[10px] text-base-content/60 hover:text-base-content h-5 min-h-0 px-1">
                        Ver todos
                    </a>
                </div>

                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 px-1">
                    <a href="{{ route('conversas-diretas.index') }}" class="avatar placeholder shrink-0" title="Iniciar nova conversa direta">
                        <div class="w-8 h-8 rounded-full border border-dashed border-base-300 hover:border-neutral text-base-content/60 flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                    </a>

                    @foreach ($minhasConversas as $itemConversa)
                        @php
                            $itemParceiro = $itemConversa->outroUtilizador(auth()->id());
                        @endphp
                        @if ($itemParceiro)
                            <a href="{{ route('conversas-diretas.show', $itemParceiro) }}" class="relative shrink-0 group" title="{{ $itemParceiro->nome }}">
                                <div class="avatar {{ $itemParceiro->avatar ? '' : 'placeholder' }}">
                                    @if ($itemParceiro->avatar)
                                        <div class="w-8 h-8 rounded-full ring-2 ring-transparent group-hover:ring-neutral transition-all overflow-hidden">
                                            <img src="{{ asset('storage/' . $itemParceiro->avatar) }}" alt="{{ $itemParceiro->nome }}" class="w-full h-full object-cover" />
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded-full text-[10px] font-bold ring-2 ring-transparent bg-base-300 text-base-content group-hover:ring-neutral flex items-center justify-center transition-all">
                                            <span>{{ mb_strtoupper(mb_substr($itemParceiro->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-between mb-2.5 px-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/50">Salas</span>
                @can('create', App\Models\Sala::class)
                    <a href="{{ route('salas.create') }}" class="btn btn-ghost btn-xs btn-square text-base-content/60 hover:text-base-content h-5 w-5 min-h-0" title="Criar nova sala">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </a>
                @endcan
            </div>

            <div class="flex-1 overflow-y-auto space-y-1.5">
                @foreach ($todasSalas as $itemSala)
                    @php
                        $ativa = $itemSala->id === $sala->id;
                    @endphp
                    <a href="{{ route('salas.show', $itemSala) }}" class="flex items-center justify-between px-3.5 py-2 rounded-full text-xs transition-all border {{ $ativa ? 'bg-neutral text-neutral-content font-bold border-neutral shadow-sm' : 'bg-base-100 hover:bg-base-200 text-base-content/80 border-base-300' }}">
                        <div class="flex items-center gap-2 truncate">
                            <span class="opacity-50 text-xs">#</span>
                            <span class="truncate">{{ $itemSala->nome }}</span>
                        </div>
                        <span class="text-[10px] opacity-60 ml-2 shrink-0">
                            {{ $itemSala->utilizadores_count }}
                        </span>
                    </a>
                @endforeach
            </div>

            @can('create', App\Models\Sala::class)
                <div class="pt-3 border-t border-base-200 mt-2">
                    <a href="{{ route('salas.create') }}" class="btn btn-outline btn-xs w-full rounded-full gap-1 border-dashed border-base-300 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Nova sala</span>
                    </a>
                </div>
            @endcan
        </aside>
    </div>

    <dialog id="modal_membros" class="modal">
        <div class="modal-box max-w-md p-6">
            <div class="flex items-center justify-between border-b border-base-200 pb-3 mb-4">
                <h3 class="font-bold text-sm">Membros de #{{ $sala->nome }}</h3>
                <form method="dialog">
                    <button class="btn btn-ghost btn-xs btn-square">✕</button>
                </form>
            </div>

            @can('manageMembers', $sala)
                @if ($utilizadoresDisponiveis->isNotEmpty())
                    <form method="POST" action="{{ route('salas.membros.store', $sala) }}" class="flex gap-2 mb-4 pb-4 border-b border-base-200">
                        @csrf
                        <select name="utilizador_id" required class="select select-bordered select-xs flex-1 text-xs">
                            <option value="" disabled selected>Adicionar colaborador...</option>
                            @foreach ($utilizadoresDisponiveis as $disp)
                                <option value="{{ $disp->id }}">{{ $disp->nome }} ({{ $disp->email }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-neutral btn-xs">Adicionar</button>
                    </form>
                @else
                    <p class="text-xs text-base-content/50 mb-4 pb-3 border-b border-base-200 italic">
                        Todos os colaboradores ativos já pertencem a esta sala.
                    </p>
                @endif
            @endcan

            <div class="space-y-2 max-h-60 overflow-y-auto">
                @foreach ($sala->utilizadores as $membro)
                    <div class="flex items-center justify-between p-2 rounded-lg bg-base-200/50 text-xs">
                        <div class="flex items-center gap-2">
                            @if ($membro->avatar)
                                <div class="avatar">
                                    <div class="w-6 h-6 rounded-full overflow-hidden">
                                        <img src="{{ asset('storage/' . $membro->avatar) }}" alt="{{ $membro->nome }}" class="w-full h-full object-cover" />
                                    </div>
                                </div>
                            @else
                                <div class="avatar placeholder">
                                    <div class="bg-neutral text-neutral-content rounded-full w-6 h-6 text-[10px] font-bold flex items-center justify-center">
                                        <span>{{ mb_strtoupper(mb_substr($membro->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                                    </div>
                                </div>
                            @endif
                            <div>
                                <span class="font-semibold block">{{ $membro->nome }}</span>
                                <span class="text-[10px] text-base-content/60">
                                    {{ $membro->id === $sala->utilizador_id ? 'Criador da sala' : $membro->permissao->label() }}
                                </span>
                            </div>
                        </div>

                        @can('manageMembers', $sala)
                            @if ($membro->id !== $sala->utilizador_id)
                                <form method="POST" action="{{ route('salas.membros.destroy', [$sala, $membro]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-xs text-error hover:bg-error/10" aria-label="Remover {{ $membro->nome }}">
                                        Remover
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
                @endforeach
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>Fechar</button>
        </form>
    </dialog>
@endsection