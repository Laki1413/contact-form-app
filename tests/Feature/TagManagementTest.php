<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン済みならタグを新規登録できる(): void
    {
        // Arrange
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $data = [
            'name' => '新しいタグ',
        ];

        // Act
        $response = $this->actingAs($user)
            ->post('/admin/tags', $data);

        // Assert
        $response->assertRedirect('/admin');

        $this->assertDatabaseHas('tags', [
            'name' => '新しいタグ',
        ]);
    }

    /** @test */
    public function ログイン済みならタグを更新できる(): void
    {
        // Arrange
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $data = [
            'name' => '更新後のタグ',
        ];

        // Act
        $response = $this->actingAs($user)
            ->put('/admin/tags/'.$tag->id, $data);

        // Assert
        $response->assertRedirect('/admin');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '更新後のタグ',
        ]);

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
            'name' => '質問',
        ]);
    }

    /** @test */
    public function ログイン済みならタグを削除できる(): void
    {
        // Arrange
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        // Act
        $response = $this->actingAs($user)
            ->delete('/admin/tags/'.$tag->id);

        // Assert
        $response->assertRedirect('/admin');

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }

    /** @test */
    public function 未ログインではタグを新規登録できない(): void
    {
        // Arrange
        $data = [
            'name' => '新しいタグ',
        ];

        // Act
        $response = $this->post('/admin/tags', $data);

        // Assert
        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('tags', [
            'name' => '新しいタグ',
        ]);
    }

    /** @test */
    public function 未ログインではタグを更新できない(): void
    {
        // Arrange
        $tag = Tag::create([
            'name' => '質問',
        ]);

        $data = [
            'name' => '更新後のタグ',
        ];

        // Act
        $response = $this->put('/admin/tags/'.$tag->id, $data);

        // Assert
        $response->assertRedirect('/login');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '質問',
        ]);
    }

    /** @test */
    public function 未ログインではタグを削除できない(): void
    {
        // Arrange
        $tag = Tag::create([
            'name' => '質問',
        ]);

        // Act
        $response = $this->delete('/admin/tags/'.$tag->id);

        // Assert
        $response->assertRedirect('/login');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '質問',
        ]);
    }
}
