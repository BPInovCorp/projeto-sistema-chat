<?php

namespace App\Http\Controllers;

use App\Enums\EstadoUtilizador;
use App\Enums\PermissaoUtilizador;
use App\Models\Utilizador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class AdminUtilizadorController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Utilizador::class);

        $utilizadores = Utilizador::orderBy('nome')->paginate(15);

        return view('admin.utilizadores.index', [
            'utilizadores' => $utilizadores,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Utilizador::class);

        return view('admin.utilizadores.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Utilizador::class);

        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('utilizadores', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'permissao' => ['required', Rule::enum(PermissaoUtilizador::class)],
            'estado' => ['required', Rule::enum(EstadoUtilizador::class)],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $caminhoAvatar = null;

        if ($request->hasFile('avatar')) {
            $caminhoAvatar = $request->file('avatar')->store('utilizadores', 'public');
        }

        try {
            Utilizador::create([
                'nome' => trim($dados['nome']),
                'email' => strtolower(trim($dados['email'])),
                'password' => $dados['password'],
                'permissao' => $dados['permissao'],
                'estado' => $dados['estado'],
                'avatar' => $caminhoAvatar,
            ]);
        } catch (Throwable $e) {
            if ($caminhoAvatar && Storage::disk('public')->exists($caminhoAvatar)) {
                Storage::disk('public')->delete($caminhoAvatar);
            }

            throw $e;
        }

        return redirect()->route('admin.utilizadores.index')->with('sucesso', 'Colaborador criado com sucesso.');
    }

    public function edit(Utilizador $utilizador): View
    {
        Gate::authorize('update', $utilizador);

        return view('admin.utilizadores.edit', [
            'utilizador' => $utilizador,
        ]);
    }

    public function update(Request $request, Utilizador $utilizador): RedirectResponse
    {
        Gate::authorize('update', $utilizador);

        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('utilizadores', 'email')->ignore($utilizador->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'permissao' => ['required', Rule::enum(PermissaoUtilizador::class)],
            'estado' => ['required', Rule::enum(EstadoUtilizador::class)],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        if ($utilizador->id === $request->user()->id) {
            if ($dados['permissao'] !== PermissaoUtilizador::ADMIN->value || $dados['estado'] !== EstadoUtilizador::ATIVO->value) {
                return back()->with('erro', 'Não pode revogar a sua própria permissão de administrador nem desativar a sua conta.')->withInput();
            }
        }

        $caminhoAvatar = $utilizador->avatar;

        if ($request->hasFile('avatar')) {
            $novoAvatar = $request->file('avatar')->store('utilizadores', 'public');
            if ($caminhoAvatar && Storage::disk('public')->exists($caminhoAvatar)) {
                Storage::disk('public')->delete($caminhoAvatar);
            }
            $caminhoAvatar = $novoAvatar;
        }

        try {
            DB::transaction(function () use ($utilizador, $dados, $caminhoAvatar) {
                if ($utilizador->isAdmin() && ($dados['permissao'] !== PermissaoUtilizador::ADMIN->value || $dados['estado'] !== EstadoUtilizador::ATIVO->value)) {
                    $outrosAdminsAtivos = Utilizador::where('permissao', PermissaoUtilizador::ADMIN)
                        ->where('estado', EstadoUtilizador::ATIVO)
                        ->where('id', '!=', $utilizador->id)
                        ->lockForUpdate()
                        ->count();

                    if ($outrosAdminsAtivos < 1) {
                        throw new RuntimeException('Operação recusada. Não é possível desativar ou despromover o último administrador ativo.');
                    }
                }

                $camposAtualizar = [
                    'nome' => trim($dados['nome']),
                    'email' => strtolower(trim($dados['email'])),
                    'permissao' => $dados['permissao'],
                    'estado' => $dados['estado'],
                    'avatar' => $caminhoAvatar,
                ];

                if (! empty($dados['password'])) {
                    $camposAtualizar['password'] = $dados['password'];
                }

                $utilizador->update($camposAtualizar);
            });
        } catch (RuntimeException $e) {
            return back()->with('erro', $e->getMessage())->withInput();
        }

        return redirect()->route('admin.utilizadores.index')->with('sucesso', 'Colaborador atualizado com sucesso.');
    }

    public function destroy(Utilizador $utilizador): RedirectResponse
    {
        Gate::authorize('delete', $utilizador);

        try {
            DB::transaction(function () use ($utilizador) {
                $outrosAdminsAtivos = Utilizador::where('permissao', PermissaoUtilizador::ADMIN)
                    ->where('estado', EstadoUtilizador::ATIVO)
                    ->where('id', '!=', $utilizador->id)
                    ->lockForUpdate()
                    ->count();

                if ($utilizador->isAdmin() && $outrosAdminsAtivos < 1) {
                    throw new RuntimeException('Não é possível desativar o último administrador ativo do sistema.');
                }

                $utilizador->update([
                    'estado' => EstadoUtilizador::INATIVO,
                ]);
            });
        } catch (RuntimeException $e) {
            return back()->with('erro', $e->getMessage());
        }

        return redirect()->route('admin.utilizadores.index')->with('sucesso', 'Conta do colaborador desativada com sucesso.');
    }
}