<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    private function createPost(array $attributes = []): Post
    {
        $category = Category::firstOrCreate(['slug' => 'news'], ['name' => 'News']);

        return Post::create([...[
            'user_id' => User::factory()->create(['name' => 'Jane Doe'])->id,
            'category_id' => $category->id,
            'title' => 'Hello World',
            'slug' => 'hello-world',
            'description' => '<p>First post</p>',
            'is_published' => true,
        ], ...$attributes]);
    }

    public function test_posts_can_be_listed(): void
    {
        $this->createPost();

        $this->getJson('/api/v1/posts')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'hello-world')
            ->assertJsonPath('data.0.description', 'First post')
            ->assertJsonPath('data.0.author.name', 'Jane Doe')
            ->assertJsonPath('data.0.category.name', 'News');
    }

    public function test_a_post_can_be_shown_by_slug(): void
    {
        $this->createPost();

        $this->getJson('/api/v1/posts/hello-world')
            ->assertOk()
            ->assertJsonPath('data.title', 'Hello World')
            ->assertJsonPath('data.isPublished', true);
    }

    public function test_unpublished_posts_are_not_listed(): void
    {
        $this->createPost();
        $this->createPost(['title' => 'Draft', 'slug' => 'draft', 'is_published' => false]);

        $this->getJson('/api/v1/posts')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'hello-world');
    }

    public function test_an_unpublished_post_returns_404(): void
    {
        $this->createPost(['is_published' => false]);

        $this->getJson('/api/v1/posts/hello-world')->assertNotFound();
    }

    public function test_an_unknown_post_returns_404(): void
    {
        $this->getJson('/api/v1/posts/does-not-exist')->assertNotFound();
    }
}
