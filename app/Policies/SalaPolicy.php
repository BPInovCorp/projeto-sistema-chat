<?php

namespace App\Policies;

use App\Models\Sala;
use App\Models\Utilizador;

class SalaPolicy
{
    public function viewAny(Utilizador $utilizador): bool
    {
        return true;
    }

    public function view(Utilizador $utilizador, Sala $sala): bool
    {
        return $sala->temMembro($utilizador->id);
    }

    public function create(Utilizador $utilizador): bool
    {
        return $utilizador->isAdmin();
    }

    public function sendMessage(Utilizador $utilizador, Sala $sala): bool
    {
        return $sala->temMembro($utilizador->id);
    }

    public function manageMembers(Utilizador $utilizador, Sala $sala): bool
    {
        return $utilizador->isAdmin();
    }
}