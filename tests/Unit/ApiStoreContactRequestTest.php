<?php

namespace Tests\Unit;

use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ApiStoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい入力の場合バリデーションが成功する(): void
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
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区1-1-1',
            'building' => 'テストマンション101',
            'category_id' => $category->id,
            'detail' => '商品について質問があります。',
            'tag_ids' => [$tag->id],
        ];

        // Act
        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        // Assert
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function 必須項目が空の場合バリデーションエラーになる(): void
    {
        // Arrange
        $request = new StoreContactRequest;

        $data = [];

        // Act
        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('first_name'));
        $this->assertTrue($validator->errors()->has('last_name'));
        $this->assertTrue($validator->errors()->has('gender'));
        $this->assertTrue($validator->errors()->has('email'));
        $this->assertTrue($validator->errors()->has('tel'));
        $this->assertTrue($validator->errors()->has('address'));
        $this->assertTrue($validator->errors()->has('category_id'));
        $this->assertTrue($validator->errors()->has('detail'));
    }

    /** @test */
    public function 電話番号の形式が不正な場合バリデーションエラーになる(): void
    {
        // Arrange
        $request = new StoreContactRequest;

        $data = [
            'tel' => '090-1234-5678',
        ];

        // Act
        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('tel'));
    }

    /** @test */
    public function 性別の値が不正な場合バリデーションエラーになる(): void
    {
        // Arrange
        $request = new StoreContactRequest;

        $data = [
            'gender' => 99,
        ];

        // Act
        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('gender'));
    }

    /** @test */
    public function 存在しないタグの場合バリデーションエラーになる(): void
    {
        // Arrange
        $request = new StoreContactRequest;

        $data = [
            'tag_ids' => [99999],
        ];

        // Act
        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('tag_ids.0'));
    }
}
