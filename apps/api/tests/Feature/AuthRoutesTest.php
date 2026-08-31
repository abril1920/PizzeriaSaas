<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Las pruebas de autenticación requieren la extensión pdo_sqlite.');
        }

        parent::setUp();
    }

    public function test_a_company_owner_can_register_and_access_a_protected_route(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'empresa_nombre' => 'Pizzería Central',
            'nit' => '901234567-8',
            'nombre' => 'Ana',
            'apellido' => 'Pérez',
            'correo' => 'ana@example.com',
            'password' => 'ClaveSegura#123',
            'password_confirmation' => 'ClaveSegura#123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('user.correo', 'ana@example.com')
            ->assertJsonStructure(['token', 'user' => ['id', 'empresa_id']]);

        $this->assertDatabaseHas('companies', ['name' => 'Pizzería Central']);
        $this->assertDatabaseHas('users', ['email' => 'ana@example.com']);

        $this->withToken($response->json('token'))
            ->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('user.correo', 'ana@example.com');
    }

    public function test_protected_routes_reject_requests_without_a_token(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized();
    }
}
