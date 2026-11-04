<?php

namespace App\Models;

use App\Enums\EstadoUtilizador;
use App\Enums\PermissaoUtilizador;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Utilizador extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'utilizadores';

    protected $fillable = [
        'nome',
        'email',
        'password',
        'avatar',
        'permissao',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'permissao' => PermissaoUtilizador::class,
            'estado' => EstadoUtilizador::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->permissao === PermissaoUtilizador::ADMIN;
    }

    public function isAtivo(): bool
    {
        return $this->estado === EstadoUtilizador::ATIVO;
    }
}