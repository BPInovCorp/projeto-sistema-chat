<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilizador_id')->constrained('utilizadores')->restrictOnDelete();
            $table->string('mensagem_tipo');
            $table->unsignedBigInteger('mensagem_id');
            $table->text('conteudo');
            $table->timestamps();

            $table->index(['mensagem_tipo', 'mensagem_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensagens');
    }
};