@extends('layouts.app')

@section('titulo', '#' . $sala->nome . ' — Inovcorp Chat')

@section('conteudo')
    <div class="flex flex-col h-[calc(100vh-8.5rem)] bg-base-100 border border-base-300 rounded-2xl shadow-sm overflow-hidden">
        <div class="navbar bg-base-100 border-b border-base-300 px-4 py-2 shrink-0 z-10 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('salas.index') }}" class="btn btn-ghost btn-xs btn-square text-base-content/70 hover:text-base-content" aria-label="Voltar à lista de salas">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

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

            <div>
                <button type="button" onclick="modal_membros.showModal()" class="btn btn-ghost btn-sm gap-1.5 text-xs font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Membros</span>
                </button>
            </div>
        </div>

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
                        <div class="chat-image avatar placeholder">
                            <div class="w-8 h-8 rounded-full text-xs font-semibold {{ $souEu ? 'bg-primary text-primary-content' : 'bg-neutral text-neutral-content' }}">
                                <span>{{ mb_strtoupper(mb_substr($mensagem->utilizador->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                            </div>
                        </div>
                        <div class="chat-header text-[11px] text-base-content/60 mb-1 flex items-center gap-1.5">
                            <span class="font-semibold text-base-content/80">{{ $mensagem->utilizador->nome }}</span>
                            <time class="text-[10px]">{{ $mensagem->created_at->format('H:i') }}</time>
                        </div>
                        <div class="chat-bubble text-sm leading-relaxed {{ $souEu ? 'chat-bubble-primary' : 'bg-base-200 text-base-content border border-base-300' }}">
                            {!! nl2br(e($mensagem->conteudo)) !!}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="p-3 md:p-4 bg-base-100 border-t border-base-300 shrink-0">
            <form method="POST" action="{{ route('salas.mensagens.store', $sala) }}" class="flex items-end gap-2">
                @csrf
                <div class="flex-1">
                    <textarea 
                        name="conteudo" 
                        required 
                        rows="1" 
                        placeholder="Escreva uma mensagem para #{{ $sala->nome }}..." 
                        class="textarea textarea-bordered textarea-sm w-full text-sm resize-none focus:outline-none focus:border-primary"
                        onkeydown="if(event.keyCode===13 && !event.shiftKey){event.preventDefault(); this.form.submit();}"
                    ></textarea>
                </div>
                <button type="submit" class="btn btn-neutral btn-sm px-4 shrink-0" aria-label="Enviar mensagem">
                    <img src="{{ asset('images/send-svgrepo-com.svg') }}" alt="Enviar mensagem" class="h-4 w-4 brightness-0 invert" />
                </button>
            </form>
        </div>
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
                @endif
            @endcan

            <div class="space-y-2 max-h-60 overflow-y-auto">
                @foreach ($sala->utilizadores as $membro)
                    <div class="flex items-center justify-between p-2 rounded-lg bg-base-200/50 text-xs">
                        <div class="flex items-center gap-2">
                            <div class="avatar placeholder">
                                <div class="bg-neutral text-neutral-content rounded-full w-6 h-6 text-[10px] font-bold">
                                    <span>{{ mb_strtoupper(mb_substr($membro->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                                </div>
                            </div>
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