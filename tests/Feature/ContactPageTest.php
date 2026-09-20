<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせフォーム入力ページが正常に表示されカテゴリとタグが渡され表示される(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200);

        $response->assertViewHas('categories', function ($categories) use ($category) {
            return $categories->contains($category);
        });

        $response->assertViewHas('tags', function ($tags) use ($tag) {
            return $tags->contains($tag);
        });

        $response->assertSee('商品のお届けについて');
        $response->assertSee('質問');
    }

    /** @test */
    public function サンクスページが正常に表示される(): void
    {
        // Act
        $response = $this->get('/thanks');

        // Assert
        $response->assertStatus(200);
    }
}
