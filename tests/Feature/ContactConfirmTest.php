<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactConfirmTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しいお問い合わせデータを送信すると確認画面が表示される(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区',
            'building' => 'テストビル101',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ];

        // Act
        $response = $this->post('/contacts/confirm', $data);

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('contact.confirm');
        $response->assertSee('山田');
        $response->assertSee('太郎');
        $response->assertSee('test@example.com');
        $response->assertSee('商品のお届けについて');
        $response->assertSee('質問');
        $response->assertSee('お問い合わせ内容です。');
    }

    /** @test */
    public function 不正なお問い合わせデータを送信するとバリデーションエラーになる(): void
    {
        // Arrange
        $data = [];

        // Act
        $response = $this->post('/contacts/confirm', $data);

        // Assert
        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'gender',
            'email',
            'tel',
            'address',
            'category_id',
            'detail',
        ]);
    }
}
