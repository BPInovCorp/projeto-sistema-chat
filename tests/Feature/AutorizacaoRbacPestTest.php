<?php

use App\Enums\EstadoUtilizador;
use App\Enums\PermissaoUtilizador;
use App\Models\Utilizador;

test('administrador pode aceder ao formulário de criação de salas', function () {
    $admin = Utilizador::create([
        'nome' => 'Bruno Pinto',
        'email' => 'admin@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::ADMIN,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $response = $this->actingAs($admin)->get('/salas/criar');

    $response->assertStatus(200);
    $response->assertSee('Criar Nova Sala');
});

test('utilizador regular é bloqueado com erro 403 ao tentar aceder à criação de salas', function () {
    $utilizador = Utilizador::create([
        'nome' => 'User Normal',
        'email' => 'user@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::UTILIZADOR,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $response = $this->actingAs($utilizador)->get('/salas/criar');

    $response->assertForbidden();
});

test('administrador pode aceder ao painel de gestão de colaboradores', function () {
    $admin = Utilizador::create([
        'nome' => 'Bruno Pinto',
        'email' => 'admin@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::ADMIN,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $response = $this->actingAs($admin)->get('/admin/utilizadores');

    $response->assertStatus(200);
    $response->assertSee('Gestão de Colaboradores');
});

test('utilizador regular é bloqueado com erro 403 ao tentar aceder ao painel de colaboradores', function () {
    $utilizador = Utilizador::create([
        'nome' => 'User Normal',
        'email' => 'user@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::UTILIZADOR,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $response = $this->actingAs($utilizador)->get('/admin/utilizadores');

    $response->assertForbidden();
});

test('administrador não pode desativar a sua própria conta', function () {
    $admin = Utilizador::create([
        'nome' => 'Bruno Pinto',
        'email' => 'admin@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::ADMIN,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    $response = $this->actingAs($admin)->delete("/admin/utilizadores/{$admin->id}");

    $response->assertForbidden();
    $this->assertAuthenticatedAs($admin);
    expect($admin->fresh()->estado)->toBe(EstadoUtilizador::ATIVO);
});