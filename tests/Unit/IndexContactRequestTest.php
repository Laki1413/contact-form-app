<?php

namespace Tests\Unit;

use App\Http\Requests\IndexContactRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい検索条件を受け付ける(): void
    {
        // Arrange
        $request = new IndexContactRequest;

        $data = [
            'keyword' => '田中',
            'gender' => 1,
            'date' => '2026-09-19',
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function 不正な性別値は拒否される(): void
    {
        // Arrange
        $request = new IndexContactRequest;

        $data = [
            'gender' => 99,
        ];

        // Act
        $validator = Validator::make($data, $request->rules());

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('gender', $validator->errors()->toArray());
    }
}
