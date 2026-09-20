<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactExportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン後にお問い合わせをcsvでエクスポートできる(): void
    {
        // Arrange
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
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
            'building' => 'テストビル101',
            'detail' => 'お問い合わせ内容です',
        ]);

        // Act
        $response = $this->actingAs($user)
            ->get('/contacts/export');

        // Assert
        $response->assertStatus(200);
        $response->assertHeader(
            'content-disposition',
            'attachment; filename="contacts.csv"'
        );

        $content = $response->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('ID,氏名,性別,メール,電話,住所,建物,カテゴリ,内容,作成日時', $content);
        $this->assertStringContainsString('山田 太郎', $content);
        $this->assertStringContainsString('男性', $content);
        $this->assertStringContainsString('商品のお届けについて', $content);
    }

    /** @test */
    public function 検索条件に一致するお問い合わせだけをcsvでエクスポートできる(): void
    {
        // Arrange
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
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
            'building' => null,
            'detail' => '男性のお問い合わせ',
        ]);

        Contact::create([
            'category_id' => $category->id,
            'first_name' => '佐藤',
            'last_name' => '花子',
            'gender' => 2,
            'email' => 'sato@example.com',
            'tel' => '08012345678',
            'address' => '東京都渋谷区',
            'building' => null,
            'detail' => '女性のお問い合わせ',
        ]);

        // Act
        $response = $this->actingAs($user)
            ->get('/contacts/export?gender=1');

        // Assert
        $response->assertStatus(200);

        $content = $response->streamedContent();

        $this->assertStringContainsString('山田 太郎', $content);
        $this->assertStringContainsString('男性', $content);
        $this->assertStringNotContainsString('佐藤 花子', $content);
        $this->assertStringNotContainsString('女性のお問い合わせ', $content);
    }
}
