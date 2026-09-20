<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しいお問い合わせデータを送信するとお問い合わせとタグが保存される(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag1 = Tag::create([
            'name' => '質問',
        ]);

        $tag2 = Tag::create([
            'name' => '要望',
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
            'tag_ids' => [
                $tag1->id,
                $tag2->id,
            ],
        ];

        // Act
        $response = $this->post('/contacts', $data);

        // Assert
        $response->assertRedirect('/thanks');

        $this->assertDatabaseHas('contacts', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => 1,
            'tag_id' => $tag1->id,
        ]);

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => 1,
            'tag_id' => $tag2->id,
        ]);
    }

    /** @test */
    public function 不正なお問い合わせデータを送信するとバリデーションエラーになる(): void
    {
        // Arrange
        $data = [];

        // Act
        $response = $this->post('/contacts', $data);

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

        $this->assertDatabaseCount('contacts', 0);
    }
}
