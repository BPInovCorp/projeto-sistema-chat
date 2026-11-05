<?php

namespace App\Policies;

use App\Enums\EstadoUtilizador;
use App\Enums\PermissaoUtilizador;
use App\Models\Utilizador;

class UtilizadorPolicy
{
    public function viewAny(Utilizador $utilizador): bool
    {
        return $utilizador->isAdmin();
    }

    public function create(Utilizador $utilizador): bool
    {
        return $utilizador->isAdmin();
    }

    public function update(Utilizador $utilizador, Utilizador $alvo): bool
    {
        return $utilizador->isAdmin();
    }

    public function delete(Utilizador $utilizador, Utilizador $alvo): bool
    {
        if (! $utilizador->isAdmin() || $utilizador->id === $alvo->id) {
            return false;
        }

        if ($alvo->isAdmin() && Utilizador::where('permissao', PermissaoUtilizador::ADMIN)->where('estado', EstadoUtilizador::ATIVO)->count() <= 1) {
            return false;
        }

        return true;
    }
}