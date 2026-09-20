<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function タグは複数のお問い合わせを持つ(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $contact1 = Contact::create([
            'category_id' => $category->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test1@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区',
            'detail' => 'お問い合わせ内容です。',
        ]);

        $contact2 = Contact::create([
            'category_id' => $category->id,
            'first_name' => '佐藤',
            'last_name' => '花子',
            'gender' => 2,
            'email' => 'test2@example.com',
            'tel' => '08012345678',
            'address' => '東京都渋谷区',
            'detail' => 'お問い合わせ内容です。',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $tag->contacts()->attach([
            $contact1->id,
            $contact2->id,
        ]);

        // Act
        $contacts = $tag->contacts;

        // Assert
        $this->assertCount(2, $contacts);
        $this->assertTrue($contacts->contains($contact1));
        $this->assertTrue($contacts->contains($contact2));
    }
}
