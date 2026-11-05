<?php

namespace App\Policies;

use App\Models\ConversaDireta;
use App\Models\Utilizador;

class ConversaDiretaPolicy
{
    public function view(Utilizador $utilizador, ConversaDireta $conversa): bool
    {
        return $conversa->temParticipante($utilizador->id);
    }

    public function sendMessage(Utilizador $utilizador, ConversaDireta $conversa): bool
    {
        return $conversa->temParticipante($utilizador->id);
    }
}