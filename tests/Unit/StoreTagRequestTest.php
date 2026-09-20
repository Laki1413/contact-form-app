<?php

namespace Tests\Unit;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreTagRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function タグ名が空だとバリデーションエラーになる(): void
    {
        // Arrange
        $request = new StoreTagRequest;

        $data = [
            'name' => '',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function タグ名が51文字以上だとバリデーションエラーになる(): void
    {
        // Arrange
        $request = new StoreTagRequest;

        $data = [
            'name' => str_repeat('あ', 51),
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function 既に使用されているタグ名はバリデーションエラーになる(): void
    {
        // Arrange
        Tag::create([
            'name' => '質問',
        ]);

        $request = new StoreTagRequest;

        $data = [
            'name' => '質問',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }
}
