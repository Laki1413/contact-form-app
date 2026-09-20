<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function キーワードでお問い合わせを検索できる(): void
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

        Contact::create([
            'category_id' => $category->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区',
            'detail' => '山田のお問い合わせです。',
        ]);

        Contact::create([
            'category_id' => $category->id,
            'first_name' => '佐藤',
            'last_name' => '花子',
            'gender' => 2,
            'email' => 'sato@example.com',
            'tel' => '08012345678',
            'address' => '東京都渋谷区',
            'detail' => '佐藤のお問い合わせです。',
        ]);

        // Act
        $response = $this->actingAs($user)->get('/admin?keyword=山田');

        // Assert
        $response->assertStatus(200);
        $response->assertSee('山田');
        $response->assertDontSee('佐藤');
    }

    /** @test */
    public function 複数条件を組み合わせてお問い合わせを検索できる(): void
    {
        // Arrange
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $category1 = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $category2 = Category::create([
            'content' => '商品の交換について',
        ]);

        Contact::create([
            'category_id' => $category1->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区',
            'detail' => 'お問い合わせ内容です。',
        ]);

        Contact::create([
            'category_id' => $category2->id,
            'first_name' => '山田',
            'last_name' => '花子',
            'gender' => 2,
            'email' => 'hanako@example.com',
            'tel' => '08012345678',
            'address' => '東京都渋谷区',
            'detail' => 'お問い合わせ内容です。',
        ]);

        // Act
        $response = $this->actingAs($user)->get(
            '/admin?keyword=山田&gender=1&category_id='.$category1->id
        );

        // Assert
        $response->assertStatus(200);
        $response->assertSee('太郎');
        $response->assertDontSee('花子');
    }

    /** @test */
    public function お問い合わせ一覧は7件ずつページネーションされる(): void
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

        for ($i = 1; $i <= 8; $i++) {
            Contact::create([
                'category_id' => $category->id,
                'first_name' => '山田',
                'last_name' => '太郎'.$i,
                'gender' => 1,
                'email' => 'test'.$i.'@example.com',
                'tel' => '09012345678',
                'address' => '東京都新宿区',
                'detail' => 'お問い合わせ内容です。',
            ]);
        }

        // Act
        $response = $this->actingAs($user)->get('/admin');

        // Assert
        $response->assertStatus(200);

        $response->assertViewHas('contacts', function ($contacts) {
            return $contacts->count() === 7
                && $contacts->total() === 8
                && $contacts->perPage() === 7;
        });
    }
}
