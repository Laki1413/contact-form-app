<?php

namespace Tests\Unit;

use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ApiIndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい検索条件の場合バリデーションが成功する(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $request = new IndexContactRequest;

        $data = [
            'keyword' => '山田',
            'gender' => 1,
            'category_id' => $category->id,
            'date' => '2026-09-20',
            'per_page' => 20,
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
    public function 性別の値が不正な場合バリデーションエラーになる(): void
    {
        // Arrange
        $request = new IndexContactRequest;

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
    public function 存在しないカテゴリーの場合バリデーションエラーになる(): void
    {
        // Arrange
        $request = new IndexContactRequest;

        $data = [
            'category_id' => 99999,
        ];

        // Act
        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('category_id'));
    }

    /** @test */
    public function 日付の値が不正な場合バリデーションエラーになる(): void
    {
        // Arrange
        $request = new IndexContactRequest;

        $data = [
            'date' => 'invalid-date',
        ];

        // Act
        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('date'));
    }

    /** @test */
    public function 表示件数が100を超える場合バリデーションエラーになる(): void
    {
        // Arrange
        $request = new IndexContactRequest;

        $data = [
            'per_page' => 101,
        ];

        // Act
        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('per_page'));
    }
}
