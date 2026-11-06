<!DOCTYPE html>
<html lang="pt-PT" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Inovcorp Chat')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 text-base-content antialiased flex flex-col">
    <header class="navbar bg-base-100 border-b border-base-300 px-4 md:px-8 shrink-0">
        <div class="flex-1">
            <a href="{{ route('salas.index') }}" class="btn btn-ghost text-sm font-bold tracking-tight gap-2 px-2">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-neutral text-neutral-content font-semibold text-xs">
                    IC
                </span>
                <span>Inovcorp Chat</span>
            </a>
        </div>

        <div class="flex-none flex items-center gap-3">
            <div class="flex items-center gap-2">
                    @if (auth()->user()->avatar)
                        <div class="avatar" aria-label="Avatar de {{ auth()->user()->nome }}">
                            <div class="w-8 h-8 rounded-full">
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->nome }}" class="object-cover" />
                            </div>
                        </div>
                    @else
                        <div class="avatar placeholder" aria-label="Avatar de {{ auth()->user()->nome }}">
                            <div class="bg-neutral text-neutral-content rounded-full w-8 h-8 text-xs font-semibold flex items-center justify-center">
                                <span>{{ mb_strtoupper(mb_substr(auth()->user()->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                            </div>
                        </div>
                    @endif
                    <div class="hidden sm:flex flex-col text-left">
                        <span class="text-xs font-semibold leading-tight">{{ auth()->user()->nome }}</span>
                        <span class="text-[10px] text-base-content/60 leading-tight">{{ auth()->user()->permissao?->label() }}</span>
                    </div>
                </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost btn-xs text-xs font-medium text-base-content/70 hover:text-error" aria-label="Terminar sessão">
                    Terminar sessão
                </button>
            </form>
        </div>
    </header>

    @if (session()->has('sucesso') || session()->has('erro'))
        <div class="max-w-4xl mx-auto w-full px-4 pt-4">
            @if (session()->has('sucesso'))
                <div role="alert" class="alert alert-success text-xs py-2.5 px-3 rounded-lg shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('sucesso') }}</span>
                </div>
            @endif

            @if (session()->has('erro'))
                <div role="alert" class="alert alert-error text-xs py-2.5 px-3 rounded-lg shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>{{ session('erro') }}</span>
                </div>
            @endif
        </div>
    @endif

    <main class="flex-1 max-w-4xl w-full mx-auto p-4 md:p-6">
        @yield('conteudo')
    </main>
</body>
</html>