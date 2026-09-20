<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせ詳細画面にお問い合わせ情報が表示される(): void
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

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
            'detail' => 'お問い合わせ内容です。',
        ]);

        $contact->tags()->attach($tag->id);

        // Act
        $response = $this->actingAs($user)
            ->get('/admin/contacts/'.$contact->id);

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.show');
        $response->assertSee('山田');
        $response->assertSee('太郎');
        $response->assertSee('男性');
        $response->assertSee('yamada@example.com');
        $response->assertSee('09012345678');
        $response->assertSee('東京都新宿区');
        $response->assertSee('テストビル101');
        $response->assertSee('商品のお届けについて');
        $response->assertSee('質問');
        $response->assertSee('お問い合わせ内容です。');
    }
}
