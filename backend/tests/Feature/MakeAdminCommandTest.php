<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\UserInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MakeAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_makes_an_existing_user_admin(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'me@example.com']);

        $this->artisan('app:make-admin', ['email' => 'me@example.com'])->assertSuccessful();

        $this->assertTrue($user->fresh()->hasRole('admin'));
        Notification::assertNothingSent();
    }

    public function test_it_invites_a_new_admin(): void
    {
        Notification::fake();

        $this->artisan('app:make-admin', ['email' => 'boss@example.com', '--name' => 'The Boss'])->assertSuccessful();

        $user = User::where('email', 'boss@example.com')->firstOrFail();

        $this->assertSame('The Boss', $user->name);
        $this->assertTrue($user->hasRole('admin'));
        $this->assertTrue($user->hasPendingInvitation());
        Notification::assertSentTo($user, UserInvitation::class);
    }
}
