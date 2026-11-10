<?php

use App\Enums\EstadoUtilizador;
use App\Enums\PermissaoUtilizador;
use App\Models\Utilizador;

test('ecrã de autenticação pode ser visualizado por convidados', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Inovcorp Chat');
});

test('utilizador ativo pode autenticar-se com credenciais corretas', function () {
    $utilizador = Utilizador::create([
        'nome' => 'Bruno Pinto',
        'email' => 'bruno@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::ADMIN,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $response = $this->post('/login', [
        'email' => 'bruno@inovcorp.pt',
        'password' => 'password123',
    ]);

    $this->assertAuthenticatedAs($utilizador);
    $response->assertRedirect('/salas');
});

test('utilizador não consegue autenticar-se com palavra-passe incorreta', function () {
    Utilizador::create([
        'nome' => 'Bruno Pinto',
        'email' => 'bruno@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::ADMIN,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $response = $this->post('/login', [
        'email' => 'bruno@inovcorp.pt',
        'password' => 'palavra-passe-errada',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

test('utilizador com conta inativa é estritamente bloqueado na autenticação', function () {
    $utilizadorInativo = Utilizador::create([
        'nome' => 'User Inativo',
        'email' => 'inativo@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::UTILIZADOR,
        'estado' => EstadoUtilizador::INATIVO,
    ]);

    $response = $this->post('/login', [
        'email' => 'inativo@inovcorp.pt',
        'password' => 'password123',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors([
        'email' => 'A sua conta encontra-se desativada. Contacte a administração.',
    ]);
    expect($utilizadorInativo->fresh()->estado)->toBe(EstadoUtilizador::INATIVO);
});

test('utilizador autenticado pode terminar sessão com sucesso', function () {
    $utilizador = Utilizador::create([
        'nome' => 'Bruno Pinto',
        'email' => 'bruno@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::ADMIN,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $response = $this->actingAs($utilizador)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/login');
});