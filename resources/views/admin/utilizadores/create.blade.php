@extends('layouts.app')

@section('titulo', 'Novo Colaborador — Inovcorp Chat')

@section('conteudo')
    <div class="max-w-xl mx-auto">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Novo Colaborador</h1>
                <p class="text-xs text-base-content/60 mt-0.5">Registe um novo membro na equipa da Inovcorp</p>
            </div>

            <a href="{{ route('admin.utilizadores.index') }}" class="btn btn-ghost btn-sm gap-1 text-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Voltar</span>
            </a>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <form method="POST" action="{{ route('admin.utilizadores.store') }}" enctype="multipart/form-data" class="card-body p-6 space-y-4">
                @csrf

                <div class="form-control">
                    <label for="nome" class="label pb-1">
                        <span class="label-text text-xs font-semibold">Nome completo</span>
                    </label>
                    <input 
                        id="nome" 
                        type="text" 
                        name="nome" 
                        value="{{ old('nome') }}" 
                        required 
                        autofocus 
                        placeholder="ex: Maria Silva" 
                        class="input input-bordered input-sm w-full text-sm @error('nome') input-error @enderror" 
                    />
                    @error('nome')
                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label for="email" class="label pb-1">
                        <span class="label-text text-xs font-semibold">Endereço de correio eletrónico</span>
                    </label>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        placeholder="colaborador@inovcorp.pt" 
                        class="input input-bordered input-sm w-full text-sm @error('email') input-error @enderror" 
                    />
                    @error('email')
                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label for="password" class="label pb-1">
                        <span class="label-text text-xs font-semibold">Palavra-passe provisória</span>
                    </label>
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        required 
                        placeholder="Mínimo de 8 caracteres" 
                        class="input input-bordered input-sm w-full text-sm @error('password') input-error @enderror" 
                    />
                    @error('password')
                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label for="permissao" class="label pb-1">
                            <span class="label-text text-xs font-semibold">Permissão de acesso</span>
                        </label>
                        <select id="permissao" name="permissao" required class="select select-bordered select-sm w-full text-xs">
                            @foreach (App\Enums\PermissaoUtilizador::cases() as $perm)
                                <option value="{{ $perm->value }}" {{ old('permissao', App\Enums\PermissaoUtilizador::UTILIZADOR->value) === $perm->value ? 'selected' : '' }}>
                                    {{ $perm->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('permissao')
                            <span class="text-xs text-error mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label for="estado" class="label pb-1">
                            <span class="label-text text-xs font-semibold">Estado inicial da conta</span>
                        </label>
                        <select id="estado" name="estado" required class="select select-bordered select-sm w-full text-xs">
                            @foreach (App\Enums\EstadoUtilizador::cases() as $est)
                                <option value="{{ $est->value }}" {{ old('estado', App\Enums\EstadoUtilizador::ATIVO->value) === $est->value ? 'selected' : '' }}>
                                    {{ $est->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('estado')
                            <span class="text-xs text-error mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-control">
                    <label for="avatar" class="label pb-1">
                        <span class="label-text text-xs font-semibold">Fotografia de perfil (opcional)</span>
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
                    <a href="{{ route('admin.utilizadores.index') }}" class="btn btn-ghost btn-sm text-xs font-medium">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-neutral btn-sm text-xs font-medium">
                        Criar colaborador
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection