<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function カテゴリは複数のお問い合わせを持つ(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        Contact::create([
            'category_id' => $category->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test1@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区',
            'detail' => 'お問い合わせ内容です。',
        ]);

        Contact::create([
            'category_id' => $category->id,
            'first_name' => '佐藤',
            'last_name' => '花子',
            'gender' => 2,
            'email' => 'test2@example.com',
            'tel' => '08012345678',
            'address' => '東京都渋谷区',
            'detail' => 'お問い合わせ内容です。',
        ]);

        // Act
        $contacts = $category->contacts;

        // Assert
        $this->assertCount(2, $contacts);
        $this->assertEquals('test1@example.com', $contacts[0]->email);
        $this->assertEquals('test2@example.com', $contacts[1]->email);
    }
}
