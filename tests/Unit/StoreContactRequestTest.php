<?php

namespace Tests\Unit;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しいお問い合わせデータを受け付ける(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $request = new StoreContactRequest;

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
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 不正な電話番号形式は拒否される(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $request = new StoreContactRequest;

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '090-1234-5678',
            'address' => '東京都新宿区',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tel', $validator->errors()->toArray());
    }
}
