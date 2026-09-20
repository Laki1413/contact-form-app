<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お問い合わせ一覧を_jso_n形式で取得できる(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        for ($i = 1; $i <= 3; $i++) {
            Contact::create([
                'category_id' => $category->id,
                'first_name' => '山田',
                'last_name' => '太郎'.$i,
                'gender' => 1,
                'email' => "test{$i}@example.com",
                'tel' => '09012345678',
                'address' => '東京都新宿区1-1-1',
                'building' => null,
                'detail' => 'お問い合わせ内容です。',
            ]);
        }

        // Act
        $response = $this->getJson('/api/v1/contacts');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'category',
                    'first_name',
                    'last_name',
                    'gender',
                    'email',
                    'tel',
                    'address',
                    'building',
                    'detail',
                    'tags',
                    'created_at',
                    'updated_at',
                ],
            ],
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ],
        ]);
    }

    /** @test */
    public function お問い合わせ一覧を検索できる(): void
    {
        // Arrange
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
            'address' => '東京都新宿区1-1-1',
            'building' => null,
            'detail' => 'お問い合わせ内容です。',
        ]);

        Contact::create([
            'category_id' => $category->id,
            'first_name' => '佐藤',
            'last_name' => '花子',
            'gender' => 2,
            'email' => 'sato@example.com',
            'tel' => '08012345678',
            'address' => '埼玉県川越市1-1-1',
            'building' => null,
            'detail' => '別のお問い合わせです。',
        ]);

        // Act
        $response = $this->getJson('/api/v1/contacts?keyword=山田');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'first_name' => '山田',
            'email' => 'yamada@example.com',
        ]);
    }

    /** @test */
    public function お問い合わせ一覧をページネーションできる(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        for ($i = 1; $i <= 3; $i++) {
            Contact::create([
                'category_id' => $category->id,
                'first_name' => '山田',
                'last_name' => '太郎'.$i,
                'gender' => 1,
                'email' => "test{$i}@example.com",
                'tel' => '09012345678',
                'address' => '東京都新宿区1-1-1',
                'building' => null,
                'detail' => 'お問い合わせ内容です。',
            ]);
        }

        // Act
        $response = $this->getJson('/api/v1/contacts?per_page=2');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonPath('meta.per_page', 2);
        $response->assertJsonPath('meta.total', 3);
        $response->assertJsonPath('meta.last_page', 2);
    }

    /** @test */
    public function お問い合わせ一覧の検索条件が不正な場合422エラーを返す(): void
    {
        // Act
        $response = $this->getJson('/api/v1/contacts?gender=99');

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'gender',
        ]);
    }

    /** @test */
    public function 特定のお問い合わせを_jso_n形式で取得できる(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区1-1-1',
            'building' => 'テストマンション101',
            'detail' => 'お問い合わせ内容です。',
        ]);

        $contact->tags()->attach($tag->id);

        // Act
        $response = $this->getJson("/api/v1/contacts/{$contact->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'category' => [
                    'id',
                    'content',
                ],
                'first_name',
                'last_name',
                'gender',
                'email',
                'tel',
                'address',
                'building',
                'detail',
                'tags' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
                'created_at',
                'updated_at',
            ],
        ]);

        $response->assertJsonFragment([
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'yamada@example.com',
        ]);

        $response->assertJsonFragment([
            'content' => '商品のお届けについて',
        ]);

        $response->assertJsonFragment([
            'name' => '質問',
        ]);
    }

    /** @test */
    public function 存在しないお問い合わせ_i_dで404エラーを返す(): void
    {
        // Act
        $response = $this->getJson('/api/v1/contacts/99999');

        // Assert
        $response->assertStatus(404);
        $response->assertJson([
            'error' => 'お問い合わせが見つかりませんでした。',
        ]);
    }

    /** @test */
    public function お問い合わせを作成できる(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区1-1-1',
            'building' => 'テストマンション101',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ];

        // Act
        $response = $this->postJson('/api/v1/contacts', $data);

        // Assert
        $response->assertStatus(201);

        $response->assertJsonFragment([
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'yamada@example.com',
        ]);

        $response->assertJsonFragment([
            'name' => '質問',
        ]);

        $this->assertDatabaseHas('contacts', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'yamada@example.com',
        ]);

        $contactId = $response->json('data.id');

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contactId,
            'tag_id' => $tag->id,
        ]);
    }

    /** @test */
    public function お問い合わせ作成時に入力が不正な場合422エラーを返す(): void
    {
        // Arrange
        $data = [
            'first_name' => '',
            'last_name' => '',
            'gender' => 99,
            'email' => 'invalid-email',
            'tel' => '090-1234-5678',
            'address' => '',
            'category_id' => 99999,
            'detail' => '',
            'tag_ids' => [99999],
        ];

        // Act
        $response = $this->postJson('/api/v1/contacts', $data);

        // Assert
        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'first_name',
            'last_name',
            'gender',
            'email',
            'tel',
            'address',
            'category_id',
            'detail',
            'tag_ids.0',
        ]);
    }

    /** @test */
    public function お問い合わせを更新できる(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $newCategory = Category::create([
            'content' => '商品の交換について',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $newTag = Tag::create([
            'name' => '要望',
        ]);

        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区1-1-1',
            'building' => null,
            'detail' => '更新前のお問い合わせです。',
        ]);

        $contact->tags()->attach($tag->id);

        $data = [
            'first_name' => '山田',
            'last_name' => '花子',
            'gender' => 2,
            'email' => 'hanako@example.com',
            'tel' => '08012345678',
            'address' => '埼玉県川越市1-1-1',
            'building' => '更新マンション202',
            'category_id' => $newCategory->id,
            'detail' => '更新後のお問い合わせです。',
            'tag_ids' => [$newTag->id],
        ];

        // Act
        $response = $this->putJson(
            "/api/v1/contacts/{$contact->id}",
            $data
        );

        // Assert
        $response->assertStatus(200);

        $response->assertJsonFragment([
            'last_name' => '花子',
            'email' => 'hanako@example.com',
            'detail' => '更新後のお問い合わせです。',
        ]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'last_name' => '花子',
            'category_id' => $newCategory->id,
        ]);

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $newTag->id,
        ]);

        $this->assertDatabaseMissing('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);
    }

    /** @test */
    public function 存在しないお問い合わせを更新すると404エラーを返す(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区1-1-1',
            'building' => null,
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
            'tag_ids' => [],
        ];

        // Act
        $response = $this->putJson('/api/v1/contacts/99999', $data);

        // Assert
        $response->assertStatus(404);
        $response->assertJson([
            'error' => 'お問い合わせが見つかりませんでした。',
        ]);
    }

    /** @test */
    public function お問い合わせ更新時に入力が不正な場合422エラーを返す(): void
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
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区1-1-1',
            'building' => null,
            'detail' => 'お問い合わせ内容です。',
        ]);

        $data = [
            'first_name' => '',
            'last_name' => '',
            'gender' => 99,
            'email' => 'invalid-email',
            'tel' => '090-1234-5678',
            'address' => '',
            'category_id' => 99999,
            'detail' => '',
            'tag_ids' => [99999],
        ];

        // Act
        $response = $this->putJson(
            "/api/v1/contacts/{$contact->id}",
            $data
        );

        // Assert
        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'first_name',
            'last_name',
            'gender',
            'email',
            'tel',
            'address',
            'category_id',
            'detail',
            'tag_ids.0',
        ]);
    }

    /** @test */
    public function お問い合わせを削除できる(): void
    {
        // Arrange
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都新宿区1-1-1',
            'building' => null,
            'detail' => 'お問い合わせ内容です。',
        ]);

        $contact->tags()->attach($tag->id);

        // Act
        $response = $this->deleteJson(
            "/api/v1/contacts/{$contact->id}"
        );

        // Assert
        $response->assertStatus(204);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);

        $this->assertDatabaseMissing('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);
    }

    /** @test */
    public function 存在しないお問い合わせを削除すると404エラーを返す(): void
    {
        // Act
        $response = $this->deleteJson('/api/v1/contacts/99999');

        // Assert
        $response->assertStatus(404);

        $response->assertJson([
            'error' => 'お問い合わせが見つかりませんでした。',
        ]);
    }
}
