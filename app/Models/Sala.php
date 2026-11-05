<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Sala extends Model
{
    use HasFactory;

    protected $table = 'salas';

    protected $fillable = [
        'nome',
        'avatar',
        'utilizador_id',
    ];

    public function criador(): BelongsTo
    {
        return $this->belongsTo(Utilizador::class, 'utilizador_id');
    }

    public function utilizadores(): BelongsToMany
    {
        return $this->belongsToMany(Utilizador::class, 'sala_utilizador', 'sala_id', 'utilizador_id')->withTimestamps();
    }

    public function mensagens(): MorphMany
    {
        return $this->morphMany(Mensagem::class, 'mensagem', 'mensagem_tipo', 'mensagem_id');
    }

    public function temMembro(int $utilizadorId): bool
    {
        return $this->utilizadores()->where('utilizadores.id', $utilizadorId)->exists();
    }
}