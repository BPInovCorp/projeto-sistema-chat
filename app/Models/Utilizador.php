<?php

namespace App\Models;

use App\Enums\EstadoUtilizador;
use App\Enums\PermissaoUtilizador;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function salas(): BelongsToMany
    {
        return $this->belongsToMany(Sala::class, 'sala_utilizador', 'utilizador_id', 'sala_id')->withTimestamps();
    }

    public function salasCriadas(): HasMany
    {
        return $this->hasMany(Sala::class, 'utilizador_id');
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(Mensagem::class, 'utilizador_id');
    }

    public function conversasComoPrimeiro(): HasMany
    {
        return $this->hasMany(ConversaDireta::class, 'utilizador_um_id');
    }

    public function conversasComoSegundo(): HasMany
    {
        return $this->hasMany(ConversaDireta::class, 'utilizador_dois_id');
    }

    public function conversasDiretas()
    {
        return ConversaDireta::where('utilizador_um_id', $this->id)
            ->orWhere('utilizador_dois_id', $this->id);
    }
}