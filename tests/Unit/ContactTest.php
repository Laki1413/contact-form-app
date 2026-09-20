<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせはカテゴリに属する(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区',
            'detail' => 'お問い合わせ内容です。',
        ]);

        // Act
        $contactCategory = $contact->category;

        // Assert
        $this->assertEquals($category->id, $contactCategory->id);
        $this->assertEquals('商品のお届けについて', $contactCategory->content);
    }

    /** @test */
    public function お問い合わせに複数のタグを紐づけられる(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区',
            'detail' => 'お問い合わせ内容です。',
        ]);

        $tag1 = Tag::create([
            'name' => '質問',
        ]);

        $tag2 = Tag::create([
            'name' => '要望',
        ]);

        // Act
        $contact->tags()->sync([
            $tag1->id,
            $tag2->id,
        ]);

        // Assert
        $this->assertCount(2, $contact->tags);
        $this->assertTrue($contact->tags->contains($tag1));
        $this->assertTrue($contact->tags->contains($tag2));
    }
}
