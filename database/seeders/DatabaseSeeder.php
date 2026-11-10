<?php

namespace Database\Seeders;

use App\Enums\EstadoUtilizador;
use App\Enums\PermissaoUtilizador;
use App\Models\ConversaDireta;
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
            ['email' => 'user1@inovcorp.pt'],
            [
                'nome' => 'User Normal 1',
                'password' => env('USER1_PASSWORD', 'user1password123'),
                'permissao' => PermissaoUtilizador::UTILIZADOR,
                'estado' => EstadoUtilizador::ATIVO,
            ]
        );

        Utilizador::updateOrCreate(
            ['email' => 'user2@inovcorp.pt'],
            [
                'nome' => 'User Normal 2',
                'password' => env('USER2_PASSWORD', 'user2password123'),
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

        if ($salaGeral->mensagens()->count() === 0) {
            $salaGeral->mensagens()->create([
                'utilizador_id' => $admin->id,
                'conteudo' => 'Bom dia equipa! Sejam bem-vindos ao novo chat da Inovcorp.',
                'created_at' => now()->subMinutes(30),
            ]);

            $salaGeral->mensagens()->create([
                'utilizador_id' => $userNormal->id,
                'conteudo' => 'Bom dia Bruno! Como está a correr o dia?',
                'created_at' => now()->subMinutes(20),
            ]);

            $salaGeral->mensagens()->create([
                'utilizador_id' => $admin->id,
                'conteudo' => 'Está a correr bem, obrigado! E você como está?',
                'created_at' => now()->subMinutes(5),
            ]);
        }

        $conversaDM = ConversaDireta::encontrarOuCriar($admin->id, $userNormal->id);

        if ($conversaDM->mensagens()->count() === 0) {
            $conversaDM->mensagens()->create([
                'utilizador_id' => $userNormal->id,
                'conteudo' => 'Olá Bruno, tens disponibilidade para rever os acessos mais tarde?',
                'created_at' => now()->subMinutes(15),
            ]);

            $conversaDM->mensagens()->create([
                'utilizador_id' => $admin->id,
                'conteudo' => 'Olá! Sim, com certeza. Está tudo configurado e a postos.',
                'created_at' => now()->subMinutes(2),
            ]);
        }
    }
}