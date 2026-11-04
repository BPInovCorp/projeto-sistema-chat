<?php

namespace App\Enums;

enum PermissaoUtilizador: string
{
    case ADMIN = 'admin';
    case UTILIZADOR = 'utilizador';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::UTILIZADOR => 'Utilizador',
        };
    }
}