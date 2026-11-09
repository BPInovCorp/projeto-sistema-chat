@extends('layouts.app')

@section('titulo', $parceiro->nome . ' — Mensagens Diretas')

@section('conteudo')
    <div class="flex flex-col h-[calc(100vh-8.5rem)] bg-base-100 border border-base-300 rounded-2xl shadow-sm overflow-hidden">
        <div class="navbar bg-base-100 border-b border-base-300 px-4 py-2 shrink-0 z-10 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('conversas-diretas.index') }}" class="btn btn-ghost btn-xs btn-square text-base-content/70 hover:text-base-content" aria-label="Voltar às mensagens diretas">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <div class="avatar {{ $parceiro->avatar ? '' : 'placeholder' }} shrink-0">
                    @if ($parceiro->avatar)
                        <div class="w-8 h-8 rounded-full">
                            <img src="{{ asset('storage/' . $parceiro->avatar) }}" alt="{{ $parceiro->nome }}" class="object-cover" />
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
        </div>

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
                                <div class="w-8 h-8 rounded-full">
                                    <img src="{{ asset('storage/' . $mensagem->utilizador->avatar) }}" alt="{{ $mensagem->utilizador->nome }}" class="object-cover" />
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-full text-xs font-semibold flex items-center justify-center {{ $souEu ? 'bg-primary text-primary-content' : 'bg-neutral text-neutral-content' }}">
                                    <span>{{ mb_strtoupper(mb_substr($mensagem->utilizador->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                                </div>
                            @endif
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
            <form method="POST" action="{{ route('conversas-diretas.mensagens.store', $conversa) }}" class="flex items-end gap-2">
                @csrf
                <div class="flex-1">
                    <textarea 
                        name="conteudo" 
                        required 
                        rows="1" 
                        placeholder="Escreva uma mensagem privada para {{ $parceiro->nome }}..." 
                        class="textarea textarea-bordered textarea-sm w-full text-sm resize-none focus:outline-none focus:border-primary"
                        onkeydown="if(event.keyCode===13 && !event.shiftKey){event.preventDefault(); this.form.submit();}"
                    ></textarea>
                </div>
                <button type="submit" class="btn btn-neutral btn-sm px-4 shrink-0" aria-label="Enviar mensagem privada">
                    <img src="{{ asset('images/send-svgrepo-com.svg') }}" alt="Enviar mensagem" class="h-4 w-4 brightness-0 invert" />
                </button>
            </form>
        </div>
    </div>
@endsection