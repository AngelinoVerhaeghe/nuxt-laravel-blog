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
            'description' => 'First post',
            'content' => "## Chapter one\n\nSome **bold** words.",
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

    public function test_a_post_shows_its_content_as_html(): void
    {
        $this->createPost();

        $this->getJson('/api/v1/posts/hello-world')
            ->assertOk()
            ->assertJsonPath('data.description', 'First post')
            ->assertJsonPath('data.content', "<h2>Chapter one</h2>\n<p>Some <strong>bold</strong> words.</p>\n");
    }

    public function test_the_list_leaves_out_the_content(): void
    {
        $this->createPost();

        $this->getJson('/api/v1/posts')
            ->assertOk()
            ->assertJsonMissingPath('data.0.content');
    }

    public function test_html_in_the_content_is_kept_but_sanitized(): void
    {
        // Older posts were written in a rich text editor and hold HTML
        $this->createPost(['content' => '<p>Old <em>post</em></p><script>alert(1)</script><a href="javascript:alert(1)">link</a>']);

        $content = $this->getJson('/api/v1/posts/hello-world')->json('data.content');

        $this->assertStringContainsString('<p>Old <em>post</em></p>', $content);
        $this->assertStringNotContainsString('<script', $content);
        $this->assertStringNotContainsString('javascript:', $content);
    }

    public function test_a_post_without_content_has_empty_content(): void
    {
        $this->createPost(['content' => null]);

        $this->getJson('/api/v1/posts/hello-world')
            ->assertOk()
            ->assertJsonPath('data.content', '');
    }

    public function test_html_is_stripped_from_the_description(): void
    {
        $this->createPost(['description' => '<p>First <b>post</b></p>']);

        $this->getJson('/api/v1/posts')->assertJsonPath('data.0.description', 'First post');
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
