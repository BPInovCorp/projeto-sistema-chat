<?php

namespace Database\Seeders;

use App\Enums\EstadoUtilizador;
use App\Enums\PermissaoUtilizador;
use App\Models\Sala;
use App\Models\Utilizador;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = Utilizador::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@inovcorp.pt')],
            [
                'nome' => 'Bruno Pinto',
                'password' => env('ADMIN_PASSWORD', 'adminpassword123'),
                'permissao' => PermissaoUtilizador::ADMIN,
                'estado' => EstadoUtilizador::ATIVO,
            ]
        );

        $userNormal = Utilizador::updateOrCreate(
            ['email' => 'user@inovcorp.pt'],
            [
                'nome' => 'User Normal',
                'password' => 'userpassword123',
                'permissao' => PermissaoUtilizador::UTILIZADOR,
                'estado' => EstadoUtilizador::ATIVO,
            ]
        );

        Utilizador::updateOrCreate(
            ['email' => 'inativo@inovcorp.pt'],
            [
                'nome' => 'User Inativo',
                'password' => 'inativopassword123',
                'permissao' => PermissaoUtilizador::UTILIZADOR,
                'estado' => EstadoUtilizador::INATIVO,
            ]
        );

        $salaGeral = Sala::updateOrCreate(
            ['nome' => 'Geral'],
            [
                'utilizador_id' => $admin->id,
            ]
        );

        $salaGeral->utilizadores()->syncWithoutDetaching([
            $admin->id,
            $userNormal->id,
        ]);

        $salaProjetos = Sala::updateOrCreate(
            ['nome' => 'Projetos'],
            [
                'utilizador_id' => $admin->id,
            ]
        );

        $salaProjetos->utilizadores()->syncWithoutDetaching([
            $admin->id,
        ]);
    }
}