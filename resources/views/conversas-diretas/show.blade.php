@extends('layouts.app')

@section('titulo', $parceiro->nome . ' — Mensagens Diretas')

@section('conteudo')
    <div class="flex gap-6 h-[calc(100vh-8.5rem)] w-full">
        <div class="flex-1 flex flex-col bg-base-100 border border-base-300 rounded-2xl shadow-sm overflow-hidden relative min-w-0">
            <header class="navbar bg-base-100 border-b border-base-300 px-4 py-2 shrink-0 z-10 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="avatar {{ $parceiro->avatar ? '' : 'placeholder' }} shrink-0">
                        @if ($parceiro->avatar)
                            <div class="w-8 h-8 rounded-full overflow-hidden">
                                <img src="{{ asset('storage/' . $parceiro->avatar) }}" alt="{{ $parceiro->nome }}" class="w-full h-full object-cover" />
                            </div>
                        @else
                            <div class="bg-neutral text-neutral-content rounded-full w-8 h-8 font-bold text-xs flex items-center justify-center">
                                <span>{{ mb_strtoupper(mb_substr($parceiro->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="font-bold text-sm leading-none">{{ $parceiro->nome }}</h1>
                            <span class="badge badge-success badge-outline badge-xs text-[9px] font-medium">
                                {{ $parceiro->estado->label() }}
                            </span>
                        </div>
                        <span class="text-[11px] text-base-content/60">
                            {{ $parceiro->email }}
                        </span>
                    </div>
                </div>

                <div>
                    <span class="badge badge-ghost badge-sm text-xs font-medium">
                        {{ $parceiro->permissao->label() }}
                    </span>
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
                        <p class="text-sm font-semibold">Inicie a conversa com {{ $parceiro->nome }}</p>
                        <p class="text-xs text-base-content/60 mt-1">As mensagens enviadas aqui são estritamente privadas entre ambos.</p>
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
                <form method="POST" action="{{ route('conversas-diretas.mensagens.store', $conversa) }}" class="max-w-3xl mx-auto bg-base-200/50 border border-base-300 rounded-full shadow-sm flex items-center px-4 py-1.5 gap-2 focus-within:border-primary focus-within:bg-base-100 transition-all">
                    @csrf
                    <input 
                        type="text" 
                        name="conteudo" 
                        required 
                        autocomplete="off" 
                        placeholder="Escreva uma mensagem privada para {{ $parceiro->nome }}..." 
                        class="input input-ghost input-sm focus:outline-none w-full text-sm px-1 border-none bg-transparent"
                    />
                    <button type="submit" class="btn btn-neutral btn-sm btn-circle shrink-0" aria-label="Enviar mensagem privada">
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
                            $ativaDM = $itemParceiro && $itemParceiro->id === $parceiro->id;
                        @endphp
                        @if ($itemParceiro)
                            <a href="{{ route('conversas-diretas.show', $itemParceiro) }}" class="relative shrink-0 group" title="{{ $itemParceiro->nome }}">
                                <div class="avatar {{ $itemParceiro->avatar ? '' : 'placeholder' }}">
                                    @if ($itemParceiro->avatar)
                                        <div class="w-8 h-8 rounded-full ring-2 {{ $ativaDM ? 'ring-neutral' : 'ring-transparent group-hover:ring-base-300' }} transition-all overflow-hidden">
                                            <img src="{{ asset('storage/' . $itemParceiro->avatar) }}" alt="{{ $itemParceiro->nome }}" class="w-full h-full object-cover" />
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded-full text-[10px] font-bold ring-2 {{ $ativaDM ? 'ring-neutral bg-neutral text-neutral-content' : 'ring-transparent bg-base-300 text-base-content group-hover:ring-neutral' }} flex items-center justify-center transition-all">
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
                    <a href="{{ route('salas.show', $itemSala) }}" class="flex items-center justify-between px-3.5 py-1.5 rounded-full text-xs transition-all border bg-base-100 hover:bg-base-200 text-base-content/80 border-base-300">
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Nova sala</span>
                    </a>
                </div>
            @endcan
        </aside>
    </div>
@endsection