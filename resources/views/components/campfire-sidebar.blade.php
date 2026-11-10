<aside class="w-72 bg-base-100 border border-base-300 rounded-2xl shadow-sm flex flex-col h-[calc(100vh-8rem)] overflow-hidden select-none">
    <div class="flex-1 overflow-y-auto p-4 space-y-6">
        <div>
            <div class="flex items-center justify-between mb-3 px-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-base-content/50">Pings</span>
                <a href="{{ route('conversas-diretas.index') }}" class="btn btn-ghost btn-xs text-[11px] text-base-content/60 hover:text-base-content">
                    Ver todos
                </a>
            </div>

            <div class="flex items-center gap-2 overflow-x-auto pb-1 px-1">
                <a href="{{ route('conversas-diretas.index') }}" class="avatar placeholder shrink-0" title="Iniciar nova conversa direta">
                    <div class="w-10 h-10 rounded-full border border-dashed border-base-300 hover:border-primary text-base-content/60 hover:text-primary flex items-center justify-center transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </a>

                @foreach ($conversas as $conversa)
                    @php
                        $parceiro = $conversa->outroUtilizador(auth()->id());
                        $ativa = request()->routeIs('conversas-diretas.show') && request()->route('utilizador')?->id === $parceiro?->id;
                    @endphp
                    @if ($parceiro)
                        <a href="{{ route('conversas-diretas.show', $parceiro) }}" class="relative shrink-0 group" title="{{ $parceiro->nome }}">
                            <div class="avatar {{ $parceiro->avatar ? '' : 'placeholder' }}">
                                @if ($parceiro->avatar)
                                    <div class="w-10 h-10 rounded-full ring-2 {{ $ativa ? 'ring-primary' : 'ring-transparent group-hover:ring-base-300' }} transition-all overflow-hidden">
                                        <img src="{{ asset('storage/' . $parceiro->avatar) }}" alt="{{ $parceiro->nome }}" class="w-full h-full object-cover" />
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-full text-xs font-bold ring-2 {{ $ativa ? 'ring-primary bg-primary text-primary-content' : 'ring-transparent bg-neutral text-neutral-content group-hover:ring-base-300' }} flex items-center justify-center transition-all">
                                        <span>{{ mb_strtoupper(mb_substr($parceiro->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-3 px-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-base-content/50">Salas</span>
            </div>

            <div class="space-y-1.5">
                @forelse ($salas as $sala)
                    @php
                        $ativa = request()->routeIs('salas.show') && request()->route('sala')?->id === $sala->id;
                    @endphp
                    <a href="{{ route('salas.show', $sala) }}" title="{{ $sala->nome }}" class="flex items-center justify-between px-4 py-2 rounded-full text-xs transition-all border {{ $ativa ? 'bg-neutral text-neutral-content font-bold border-neutral shadow-sm' : 'bg-base-100 hover:bg-base-200 text-base-content/80 border-base-300' }}">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="opacity-50 text-xs">#</span>
                            <span class="truncate">{{ $sala->nome }}</span>
                        </div>
                    </a>
                @empty
                    <div class="p-3 text-center text-xs text-base-content/50 rounded-2xl bg-base-200/40">
                        Nenhuma sala associada
                    </div>
                @endforelse
            </div>

            @can('create', App\Models\Sala::class)
                <div class="pt-4 flex justify-center">
                    <a href="{{ route('salas.create') }}" class="btn btn-circle btn-sm btn-outline border-base-300 hover:bg-neutral hover:text-neutral-content hover:border-neutral text-base-content/70 shadow-sm" title="Criar nova sala" aria-label="Criar nova sala">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </a>
                </div>
            @endcan
        </div>
    </div>

    <div class="p-3.5 border-t border-base-300 bg-base-100 flex items-center justify-between gap-2 shrink-0">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="avatar {{ auth()->user()->avatar ? '' : 'placeholder' }} shrink-0">
                @if (auth()->user()->avatar)
                    <div class="w-8 h-8 rounded-full overflow-hidden">
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->nome }}" class="w-full h-full object-cover" />
                    </div>
                @else
                    <div class="bg-neutral text-neutral-content rounded-full w-8 h-8 font-bold text-xs flex items-center justify-center">
                        <span>{{ mb_strtoupper(mb_substr(auth()->user()->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                    </div>
                @endif
            </div>

            <div class="min-w-0">
                <span class="text-xs font-bold truncate block leading-tight">{{ auth()->user()->nome }}</span>
                <span class="text-[10px] text-base-content/60 truncate block leading-tight">{{ auth()->user()->permissao->label() }}</span>
            </div>
        </div>

        <div class="flex items-center gap-1 shrink-0">
            @can('viewAny', App\Models\Utilizador::class)
                <a href="{{ route('admin.utilizadores.index') }}" class="btn btn-ghost btn-xs btn-square text-base-content/60 hover:text-base-content" title="Gestão de Colaboradores" aria-label="Gestão de Colaboradores">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </a>
            @endcan

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost btn-xs btn-square text-base-content/60 hover:text-error" title="Terminar sessão" aria-label="Terminar sessão">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>