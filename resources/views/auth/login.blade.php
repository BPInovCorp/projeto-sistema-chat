<!DOCTYPE html>
<html lang="pt-PT" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sessão — Inovcorp Chat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 flex items-center justify-center p-4 antialiased">
    <div class="card w-full max-w-sm bg-base-100 shadow-sm border border-base-300">
        <div class="card-body p-8">
            <div class="mb-6 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-neutral text-neutral-content mb-3 font-semibold text-lg">
                    IC
                </div>
                <h1 class="text-xl font-bold tracking-tight">Inovcorp Chat</h1>
                <p class="text-xs text-base-content/60 mt-1">Sistema de conversação de equipa</p>
            </div>

            @if ($errors->has('email') && old('email') && !in_array($errors->first('email'), ['The email field is required.', 'O campo endereço de correio eletrónico é obrigatório.']))
                <div role="alert" class="alert alert-error text-xs py-2 px-3 mb-4 rounded-lg flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ $errors->first('email') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                @csrf

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
                        autofocus 
                        placeholder="nome@exemplo.com" 
                        class="input input-bordered input-sm w-full text-sm @error('email') input-error @enderror" 
                    />
                    @error('email')
                        @if (empty(old('email')))
                            <span class="text-xs text-error mt-1">{{ $message }}</span>
                        @endif
                    @enderror
                </div>

                <div class="form-control">
                    <label for="password" class="label pb-1">
                        <span class="label-text text-xs font-semibold">Palavra-passe</span>
                    </label>
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        required 
                        placeholder="••••••••" 
                        class="input input-bordered input-sm w-full text-sm @error('password') input-error @enderror" 
                    />
                    @error('password')
                        <span class="text-xs text-error mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-2 py-0">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            class="checkbox checkbox-xs rounded" 
                        />
                        <span class="label-text text-xs text-base-content/80">Manter sessão iniciada</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn btn-neutral btn-sm w-full font-medium">
                        Iniciar sessão
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>