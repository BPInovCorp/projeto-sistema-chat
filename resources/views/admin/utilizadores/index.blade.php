@extends('layouts.app')

@section('titulo', 'Gestão de Colaboradores — Inovcorp Chat')

@section('conteudo')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight">Gestão de Colaboradores</h1>
            <p class="text-xs text-base-content/60 mt-0.5">Painel de administração da equipa e controlo de acessos</p>
        </div>

        <div>
            <a href="{{ route('admin.utilizadores.create') }}" class="btn btn-neutral btn-sm gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Novo Colaborador</span>
            </a>
        </div>
    </div>

    <div class="card bg-base-100 border border-base-300 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr class="bg-base-200/50 text-xs text-base-content/70">
                        <th>Colaborador</th>
                        <th>Permissão</th>
                        <th>Estado</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($utilizadores as $u)
                        <tr class="hover:bg-base-200/30">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar {{ $u->avatar ? '' : 'placeholder' }} shrink-0">
                                        @if ($u->avatar)
                                            <div class="w-8 h-8 rounded-full">
                                                <img src="{{ asset('storage/' . $u->avatar) }}" alt="{{ $u->nome }}" class="object-cover" />
                                            </div>
                                        @else
                                            <div class="bg-neutral text-neutral-content rounded-full w-8 h-8 font-bold text-xs flex items-center justify-center">
                                                <span>{{ mb_strtoupper(mb_substr($u->nome, 0, 2, 'UTF-8'), 'UTF-8') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-xs">{{ $u->nome }}</div>
                                        <div class="text-[11px] text-base-content/60">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $u->isAdmin() ? 'badge-neutral' : 'badge-ghost' }} badge-xs font-medium">
                                    {{ $u->permissao->label() }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $u->isAtivo() ? 'badge-success badge-outline' : 'badge-error badge-outline' }} badge-xs font-medium">
                                    {{ $u->estado->label() }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.utilizadores.edit', $u) }}" class="btn btn-ghost btn-xs text-xs">
                                        Editar
                                    </a>

                                    @can('delete', $u)
                                        @if ($u->isAtivo())
                                            <button type="button" onclick="document.getElementById('modal_desativar_{{ $u->id }}').showModal()" class="btn btn-ghost btn-xs text-error hover:bg-error/10 text-xs">
                                                Desativar
                                            </button>

                                            <dialog id="modal_desativar_{{ $u->id }}" class="modal text-left">
                                                <div class="modal-box max-w-sm p-6">
                                                    <h3 class="font-bold text-sm text-error">Desativar Colaborador</h3>
                                                    <p class="text-xs text-base-content/70 mt-2">
                                                        Tem a certeza de que pretende desativar a conta de <strong class="text-base-content">{{ $u->nome }}</strong>? O colaborador perderá imediatamente o acesso à aplicação.
                                                    </p>
                                                    <div class="modal-action flex justify-end gap-2 mt-4 pt-3 border-t border-base-200">
                                                        <form method="dialog">
                                                            <button class="btn btn-ghost btn-xs text-xs">Cancelar</button>
                                                        </form>
                                                        <form method="POST" action="{{ route('admin.utilizadores.destroy', $u) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-error btn-xs text-xs text-white">
                                                                Confirmar Desativação
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <form method="dialog" class="modal-backdrop">
                                                    <button>Fechar</button>
                                                </form>
                                            </dialog>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($utilizadores->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $utilizadores->links() }}
            </div>
        @endif
    </div>
@endsection