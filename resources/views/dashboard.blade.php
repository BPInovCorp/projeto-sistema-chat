<!DOCTYPE html>
<html lang="pt-PT" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel — Inovcorp Chat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 flex items-center justify-center p-4 antialiased">
    <div class="card w-full max-w-md bg-base-100 shadow-sm border border-base-300">
        <div class="card-body p-8 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-neutral text-neutral-content mx-auto mb-3 font-semibold text-lg">
                {{ substr($utilizador->nome, 0, 2) }}
            </div>
            <h1 class="text-xl font-bold tracking-tight">{{ $utilizador->nome }}</h1>
            <p class="text-xs text-base-content/60 mt-1">{{ $utilizador->email }}</p>

            <div class="my-4">
                <span class="badge badge-neutral text-xs font-medium">
                    {{ $utilizador->permissao->label() }}
                </span>
                <span class="badge badge-success badge-outline text-xs font-medium ml-1">
                    {{ $utilizador->estado->label() }}
                </span>
            </div>

            <div class="pt-4 border-t border-base-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm w-full font-medium">
                        Terminar sessão
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>