<?php

namespace Tests\Unit;

use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateTagRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 現在のタグと同じ名前を使用できる(): void
    {
        // Arrange
        $tag = Tag::create([
            'name' => '質問',
        ]);

        $request = new UpdateTagRequest;
        $request->setRouteResolver(function () use ($tag) {
            return new class($tag)
            {
                public function __construct(private Tag $tag) {}

                public function parameter($key)
                {
                    return $key === 'tag' ? $this->tag : null;
                }
            };
        });

        $data = [
            'name' => '質問',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 他のタグと同じ名前はバリデーションエラーになる(): void
    {
        // Arrange
        $tag = Tag::create([
            'name' => '質問',
        ]);

        Tag::create([
            'name' => '要望',
        ]);

        $request = new UpdateTagRequest;
        $request->setRouteResolver(function () use ($tag) {
            return new class($tag)
            {
                public function __construct(private Tag $tag) {}

                public function parameter($key)
                {
                    return $key === 'tag' ? $this->tag : null;
                }
            };
        });

        $data = [
            'name' => '要望',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }
}
