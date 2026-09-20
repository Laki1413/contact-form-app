<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未ログインでは管理画面にアクセスできない(): void
    {
        // Act
        $response = $this->get('/admin');

        // Assert
        $response->assertRedirect('/login');
    }

    /** @test */
    public function ログイン済みなら管理画面にアクセスできる(): void
    {
        // Arrange
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Act
        $response = $this->actingAs($user)->get('/admin');

        // Assert
        $response->assertStatus(200);
    }
}
