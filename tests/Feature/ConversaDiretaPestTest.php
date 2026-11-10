<?php

use App\Enums\EstadoUtilizador;
use App\Enums\PermissaoUtilizador;
use App\Models\ConversaDireta;
use App\Models\Utilizador;
use Illuminate\Support\Facades\Gate;

test('algoritmo ordena identificadores e garante conversa única entre o mesmo par de colaboradores', function () {
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

    $conversa1 = ConversaDireta::encontrarOuCriar($userB->id, $userA->id);
    $conversa2 = ConversaDireta::encontrarOuCriar($userA->id, $userB->id);

    expect($conversa1->id)->toBe($conversa2->id);
    expect(ConversaDireta::count())->toBe(1);
    expect($conversa1->utilizador_um_id)->toBeLessThan($conversa1->utilizador_dois_id);
});

test('sistema impede utilizador de criar conversa direta consigo próprio', function () {
    $user = Utilizador::create([
        'nome' => 'Bruno Pinto',
        'email' => 'bruno@inovcorp.pt',
        'password' => 'password123',
        'permissao' => PermissaoUtilizador::ADMIN,
        'estado' => EstadoUtilizador::ATIVO,
    ]);

    expect(fn () => ConversaDireta::encontrarOuCriar($user->id, $user->id))
        ->toThrow(InvalidArgumentException::class, 'Não é possível criar uma conversa direta com o próprio utilizador.');

    $response = $this->actingAs($user)->get("/conversas-diretas/{$user->id}");
    $response->assertStatus(400);
});

test('participantes acedem com sucesso ao ecrã da sua conversa direta partilhada', function () {
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

    $response = $this->actingAs($userA)->get("/conversas-diretas/{$userB->id}");

    $response->assertStatus(200);
    $response->assertSee($userB->nome);
});

test('terceiro utilizador não tem autorização para aceder à conversa privada de outros colegas', function () {
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

    expect(Gate::forUser($userC)->allows('view', $conversa))->toBeFalse();
    expect(Gate::forUser($userC)->allows('sendMessage', $conversa))->toBeFalse();
    expect(Gate::forUser($userA)->allows('view', $conversa))->toBeTrue();
    expect(Gate::forUser($userB)->allows('view', $conversa))->toBeTrue();
});