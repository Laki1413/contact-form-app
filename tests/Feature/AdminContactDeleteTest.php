<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせを削除できる(): void
    {
        // Arrange
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区',
            'detail' => 'お問い合わせ内容です。',
        ]);

        // Act
        $response = $this->actingAs($user)
            ->delete('/admin/contacts/'.$contact->id);

        // Assert
        $response->assertRedirect('/admin');

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }
}
