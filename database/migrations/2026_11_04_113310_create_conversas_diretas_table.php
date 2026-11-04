<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversas_diretas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilizador_um_id')->constrained('utilizadores')->restrictOnDelete();
            $table->foreignId('utilizador_dois_id')->constrained('utilizadores')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['utilizador_um_id', 'utilizador_dois_id']);
        });

        DB::statement('ALTER TABLE conversas_diretas ADD CONSTRAINT chk_conversa_ordem CHECK (utilizador_um_id < utilizador_dois_id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('conversas_diretas');
    }
};