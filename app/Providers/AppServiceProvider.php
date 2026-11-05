<?php

namespace App\Providers;

use App\Models\ConversaDireta;
use App\Models\Sala;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Relation::morphMap([
            'sala' => Sala::class,
            'conversa_direta' => ConversaDireta::class,
        ]);
    }
}
