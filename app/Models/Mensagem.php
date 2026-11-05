<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Mensagem extends Model
{
    use HasFactory;

    protected $table = 'mensagens';

    protected $fillable = [
        'utilizador_id',
        'mensagem_tipo',
        'mensagem_id',
        'conteudo',
    ];

    public function utilizador(): BelongsTo
    {
        return $this->belongsTo(Utilizador::class, 'utilizador_id');
    }

    public function destino(): MorphTo
    {
        return $this->morphTo('mensagem', 'mensagem_tipo', 'mensagem_id');
    }

    public function scopeCronologico(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc')->orderBy('id', 'asc');
    }
}