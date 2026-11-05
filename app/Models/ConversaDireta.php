<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use InvalidArgumentException;

class ConversaDireta extends Model
{
    use HasFactory;

    protected $table = 'conversas_diretas';

    protected $fillable = [
        'utilizador_um_id',
        'utilizador_dois_id',
    ];

    public function primeiroUtilizador(): BelongsTo
    {
        return $this->belongsTo(Utilizador::class, 'utilizador_um_id');
    }

    public function segundoUtilizador(): BelongsTo
    {
        return $this->belongsTo(Utilizador::class, 'utilizador_dois_id');
    }

    public function mensagens(): MorphMany
    {
        return $this->morphMany(Mensagem::class, 'mensagem', 'mensagem_tipo', 'mensagem_id');
    }

    public function temParticipante(int $utilizadorId): bool
    {
        return $this->utilizador_um_id === $utilizadorId || $this->utilizador_dois_id === $utilizadorId;
    }

    public function outroUtilizador(int $utilizadorAutenticadoId): ?Utilizador
    {
        if ($this->utilizador_um_id === $utilizadorAutenticadoId) {
            return $this->segundoUtilizador;
        }

        if ($this->utilizador_dois_id === $utilizadorAutenticadoId) {
            return $this->primeiroUtilizador;
        }

        return null;
    }

    public static function encontrarOuCriar(int $utilizadorA, int $utilizadorB): self
    {
        if ($utilizadorA === $utilizadorB) {
            throw new InvalidArgumentException('Não é possível criar uma conversa direta com o próprio utilizador.');
        }

        $primeiroId = min($utilizadorA, $utilizadorB);
        $segundoId = max($utilizadorA, $utilizadorB);

        return self::firstOrCreate([
            'utilizador_um_id' => $primeiroId,
            'utilizador_dois_id' => $segundoId,
        ]);
    }
}