@extends('layouts.app')

@section('titulo', 'Editar #' . $sala->nome . ' — Inovcorp Chat')

@section('conteudo')
    <div class="max-w-xl mx-auto space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Editar Sala</h1>
                <p class="text-xs text-base-content/60 mt-0.5">Atualize os detalhes do canal de conversação #{{ $sala->nome }}</p>
            </div>

            <a href="{{ route('salas.show', $sala) }}" class="btn btn-ghost btn-sm gap-1 text-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Voltar à sala</span>
            </a>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <form method="POST" action="{{ route('salas.update', $sala) }}" enctype="multipart/form-data" class="card-body p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="form-control">
                    <label for="nome" class="label pb-1">
                        <span class="label-text text-xs font-semibold">Nome da sala</span>
                    </label>
                    <input 
                        id="nome" 
                        type="text" 
                        name="nome" 
                        value="{{ old('nome', $sala->nome) }}" 
                        required 
                        autofocus 
                        class="input input-bordered input-sm w-full text-sm @error('nome') input-error @enderror" 
                    />
                    @error('nome')
                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label for="avatar" class="label pb-1">
                        <span class="label-text text-xs font-semibold">Alterar imagem ou ícone (opcional)</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <div class="avatar {{ $sala->avatar ? '' : 'placeholder' }} shrink-0">
                            @if ($sala->avatar)
                                <div class="w-10 h-10 rounded-lg overflow-hidden">
                                    <img src="{{ asset('storage/' . $sala->avatar) }}" alt="{{ $sala->nome }}" class="w-full h-full object-cover" />
                                </div>
                            @else
                                <div class="bg-neutral text-neutral-content rounded-lg w-10 h-10 font-bold text-xs flex items-center justify-center">
                                    <span>{{ mb_strtoupper(mb_substr($sala->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                                </div>
                            @endif
                        </div>
                        <input 
                            id="avatar" 
                            type="file" 
                            name="avatar" 
                            accept="image/png, image/jpeg, image/webp" 
                            class="file-input file-input-bordered file-input-sm w-full text-xs @error('avatar') file-input-error @enderror" 
                        />
                    </div>
                    <span class="text-[11px] text-base-content/60 mt-1">Formatos suportados: JPG, PNG ou WebP (máx. 2MB).</span>
                    @error('avatar')
                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-base-200">
                    <a href="{{ route('salas.show', $sala) }}" class="btn btn-ghost btn-sm text-xs font-medium">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-neutral btn-sm text-xs font-medium">
                        Guardar alterações
                    </button>
                </div>
            </form>
        </div>

        @can('delete', $sala)
            <div class="card bg-base-100 border border-error/20 p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-sm font-bold text-error">Zona de Perigo</h2>
                        <p class="text-xs text-base-content/60 mt-0.5">Eliminar permanentemente esta sala e todo o seu histórico</p>
                    </div>
                    <button type="button" onclick="modal_eliminar_sala.showModal()" class="btn btn-error btn-outline btn-sm text-xs">
                        Eliminar sala
                    </button>
                </div>
            </div>

            <dialog id="modal_eliminar_sala" class="modal text-left">
                <div class="modal-box max-w-sm p-6">
                    <h3 class="font-bold text-sm text-error">Eliminar Sala</h3>
                    <p class="text-xs text-base-content/70 mt-2">
                        Tem a certeza de que pretende eliminar permanentemente a sala <strong class="text-base-content">#{{ $sala->nome }}</strong>? Todas as mensagens e associações de membros serão removidas. Esta ação é irreversível.
                    </p>
                    <div class="modal-action flex justify-end gap-2 mt-4 pt-3 border-t border-base-200">
                        <form method="dialog">
                            <button class="btn btn-ghost btn-xs text-xs">Cancelar</button>
                        </form>
                        <form method="POST" action="{{ route('salas.destroy', $sala) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-error btn-xs text-xs text-white">
                                Sim, Eliminar Sala
                            </button>
                        </form>
                    </div>
                </div>
                <form method="dialog" class="modal-backdrop">
                    <button>Fechar</button>
                </form>
            </dialog>
        @endcan
    </div>
@endsection