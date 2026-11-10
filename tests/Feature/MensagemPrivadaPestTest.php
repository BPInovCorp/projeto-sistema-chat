<?php

use App\Enums\EstadoUtilizador;
use App\Enums\PermissaoUtilizador;
use App\Models\ConversaDireta;
use App\Models\Sala;
use App\Models\Utilizador;

test('membro de uma sala pode enviar mensagem com persistência polimórfica associada', function () {
    $user = Utilizador::create([
        'nome' => 'Bruno Pinto',
        'email' => 'bruno@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::ADMIN,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $sala = Sala::create([
        'nome' => 'Geral',
        'utilizador_id' => $user->id,
    ]);
    $sala->utilizadores()->attach($user->id);

    $response = $this->actingAs($user)->post("/salas/{$sala->id}/mensagens", [
        'conteudo' => 'Olá equipa!',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('mensagens', [
        'utilizador_id' => $user->id,
        'mensagem_tipo' => 'sala',
        'mensagem_id' => $sala->id,
        'conteudo' => 'Olá equipa!',
    ]);
});

test('participante de conversa direta pode enviar mensagem privada com persistência polimórfica associada', function () {
    $userA = Utilizador::create([
        'nome' => 'Bruno Pinto',
        'email' => 'bruno@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::ADMIN,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $userB = Utilizador::create([
        'nome' => 'User Normal',
        'email' => 'user@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::UTILIZADOR,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $conversa = ConversaDireta::encontrarOuCriar($userA->id, $userB->id);

    $response = $this->actingAs($userA)->post("/conversas-diretas/{$conversa->id}/mensagens", [
        'conteudo' => 'Olá em privado!',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('mensagens', [
        'utilizador_id' => $userA->id,
        'mensagem_tipo' => 'conversa_direta',
        'mensagem_id' => $conversa->id,
        'conteudo' => 'Olá em privado!',
    ]);
});

test('mensagens compostas exclusivamente por espaços em branco são rejeitadas', function () {
    $user = Utilizador::create([
        'nome' => 'Bruno Pinto',
        'email' => 'bruno@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::ADMIN,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $sala = Sala::create([
        'nome' => 'Geral',
        'utilizador_id' => $user->id,
    ]);
    $sala->utilizadores()->attach($user->id);

    $response = $this->actingAs($user)->post("/salas/{$sala->id}/mensagens", [
        'conteudo' => '     ',
    ]);

    $response->assertSessionHasErrors('conteudo');
    $this->assertDatabaseCount('mensagens', 0);
});

test('utilizador não participante é bloqueado com erro 403 ao tentar enviar mensagem privada', function () {
    $userA = Utilizador::create([
        'nome' => 'Bruno Pinto',
        'email' => 'bruno@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::ADMIN,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $userB = Utilizador::create([
        'nome' => 'User Normal',
        'email' => 'user@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::UTILIZADOR,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $userC = Utilizador::create([
        'nome' => 'Terceiro Colaborador',
        'email' => 'terceiro@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::UTILIZADOR,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $conversa = ConversaDireta::encontrarOuCriar($userA->id, $userB->id);

    $response = $this->actingAs($userC)->post("/conversas-diretas/{$conversa->id}/mensagens", [
        'conteudo' => 'Tentativa de intrusão',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseCount('mensagens', 0);
});