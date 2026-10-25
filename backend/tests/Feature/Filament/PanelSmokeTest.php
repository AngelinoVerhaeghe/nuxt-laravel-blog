<?php

namespace Tests\Feature\Filament;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PanelSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_login_page_renders(): void
    {
        $this->get('/dashboard/login')->assertOk();
    }

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/dashboard')->assertRedirect('/dashboard/login');
    }

    public function test_the_dashboard_renders(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk();
    }

    public static function resourcePaths(): array
    {
        return [
            'articles' => ['/dashboard/articles'],
            'brands' => ['/dashboard/brands'],
            'categories' => ['/dashboard/categories'],
            'customers' => ['/dashboard/customers'],
            'orders' => ['/dashboard/orders'],
            'permissions' => ['/dashboard/permissions'],
            'posts' => ['/dashboard/posts'],
            'products' => ['/dashboard/products'],
            'roles' => ['/dashboard/roles'],
            'tags' => ['/dashboard/tags'],
            'users' => ['/dashboard/users'],
        ];
    }

    #[DataProvider('resourcePaths')]
    public function test_resource_index_pages_render(string $path): void
    {
        $this->actingAs(User::factory()->create())
            ->get($path)
            ->assertOk();
    }

    #[DataProvider('resourcePaths')]
    public function test_resource_create_pages_render(string $path): void
    {
        $this->actingAs(User::factory()->create())
            ->get($path.'/create')
            ->assertOk();
    }

    public function test_the_post_edit_page_renders(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'News', 'slug' => 'news']);
        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Hello World',
            'slug' => 'hello-world',
            'description' => 'First post',
        ]);

        $this->actingAs($user)
            ->get("/dashboard/posts/{$post->slug}/edit")
            ->assertOk()
            ->assertSee('Hello World');
    }
}
