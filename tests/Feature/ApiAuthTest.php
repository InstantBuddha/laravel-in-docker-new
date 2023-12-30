<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_user_login()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('reallySecretPassword123'),
        ]);

        $response = $this->json('POST', '/api/auth/login', [
            'email' => $user->email,
            'password' => 'reallySecretPassword123',
        ]);

        $response->assertStatus(200);

        $this->actingAs($user);

        $response = $this->get('/api/auth/something');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_access()
    {
        $response = $this->json('POST', '/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'IdontEvenExist',
        ]);

        $response->assertStatus(401);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/auth/something');

        $response->assertStatus(401);
    }
}
