@extends('layouts.app')

@section('titulo', 'Criar Nova Sala — Inovcorp Chat')

@section('conteudo')
    <div class="max-w-xl mx-auto">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Criar Nova Sala</h1>
                <p class="text-xs text-base-content/60 mt-0.5">Configure um novo canal de conversação para a equipa</p>
            </div>

            <a href="{{ route('salas.index') }}" class="btn btn-ghost btn-sm gap-1 text-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Voltar</span>
            </a>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <form method="POST" action="{{ route('salas.store') }}" enctype="multipart/form-data" class="card-body p-6 space-y-4">
                @csrf

                <div class="form-control">
                    <label for="nome" class="label pb-1">
                        <span class="label-text text-xs font-semibold">Nome da sala</span>
                    </label>
                    <input 
                        id="nome" 
                        type="text" 
                        name="nome" 
                        value="{{ old('nome') }}" 
                        required 
                        autofocus 
                        placeholder="ex: Geral, Marketing, Projetos" 
                        class="input input-bordered input-sm w-full text-sm @error('nome') input-error @enderror" 
                    />
                    @error('nome')
                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label for="avatar" class="label pb-1">
                        <span class="label-text text-xs font-semibold">Imagem ou ícone da sala (opcional)</span>
                    </label>
                    <input 
                        id="avatar" 
                        type="file" 
                        name="avatar" 
                        accept="image/png, image/jpeg, image/webp" 
                        class="file-input file-input-bordered file-input-sm w-full text-xs @error('avatar') file-input-error @enderror" 
                    />
                    <span class="text-[11px] text-base-content/60 mt-1">Formatos suportados: JPG, PNG ou WebP (máx. 2MB).</span>
                    @error('avatar')
                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-base-200">
                    <a href="{{ route('salas.index') }}" class="btn btn-ghost btn-sm text-xs font-medium">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-neutral btn-sm text-xs font-medium">
                        Criar sala
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection