<?php

namespace Tests\Feature\Filament;

use App\Filament\Auth\AcceptInvitation;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use App\Notifications\UserInvitation;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserInvitationTest extends TestCase
{
    use RefreshDatabase;

    private function invitedUser(): User
    {
        return User::factory()->unverified()->role('editor')->create([
            'email' => 'new@example.com',
            'password' => 'not-chosen-by-the-user',
        ]);
    }

    private function inviteUrlFor(User $user): string
    {
        Notification::fake();

        $user->sendInvitation();

        $url = null;
        Notification::assertSentTo($user, UserInvitation::class, function (UserInvitation $notification) use (&$url) {
            $url = $notification->url;

            return true;
        });

        return $url;
    }

    public function test_creating_a_user_sends_an_invitation(): void
    {
        Notification::fake();

        $this->actingAs(User::factory()->role('admin')->create());

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'New Editor',
                'email' => 'new@example.com',
                'roles' => [Role::findByName('editor')->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('email', 'new@example.com')->firstOrFail();

        $this->assertTrue($user->hasRole('editor'));
        $this->assertTrue($user->hasPendingInvitation());
        Notification::assertSentTo($user, UserInvitation::class, function (UserInvitation $notification) {
            return str_contains($notification->url, '/dashboard/invitation/accept');
        });
    }

    public function test_a_role_is_required_when_creating_a_user(): void
    {
        $this->actingAs(User::factory()->role('admin')->create());

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'No Role',
                'email' => 'norole@example.com',
                'roles' => [],
            ])
            ->call('create')
            ->assertHasFormErrors(['roles' => 'required']);
    }

    public function test_the_invitation_page_requires_a_signed_link(): void
    {
        $user = $this->invitedUser();

        $this->get($this->inviteUrlFor($user))->assertOk()->assertSee('Activate account');
        $this->get('/dashboard/invitation/accept?email=new@example.com&token=abc')->assertForbidden();
    }

    public function test_accepting_an_invitation_sets_the_password_and_logs_in(): void
    {
        $user = $this->invitedUser();
        $token = Password::broker('invites')->createToken($user);

        Livewire::test(AcceptInvitation::class, ['email' => $user->email, 'token' => $token])
            ->fillForm([
                'password' => 'my-new-password',
                'passwordConfirmation' => 'my-new-password',
            ])
            ->call('resetPassword')
            ->assertHasNoFormErrors()
            ->assertRedirect('/dashboard');

        $user->refresh();

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('my-new-password', $user->password));
        $this->assertFalse($user->hasPendingInvitation());
    }

    public function test_an_invitation_can_only_be_used_once(): void
    {
        $user = $this->invitedUser();
        $token = Password::broker('invites')->createToken($user);

        Livewire::test(AcceptInvitation::class, ['email' => $user->email, 'token' => $token])
            ->fillForm(['password' => 'first-password', 'passwordConfirmation' => 'first-password'])
            ->call('resetPassword');

        auth()->logout();

        Livewire::test(AcceptInvitation::class, ['email' => $user->email, 'token' => $token])
            ->fillForm(['password' => 'second-password', 'passwordConfirmation' => 'second-password'])
            ->call('resetPassword')
            ->assertNoRedirect();

        $this->assertGuest();
        $this->assertTrue(Hash::check('first-password', $user->fresh()->password));
    }

    public function test_an_expired_invitation_is_rejected(): void
    {
        $user = $this->invitedUser();
        $token = Password::broker('invites')->createToken($user);

        $this->travel(49)->hours();

        Livewire::test(AcceptInvitation::class, ['email' => $user->email, 'token' => $token])
            ->fillForm(['password' => 'my-new-password', 'passwordConfirmation' => 'my-new-password'])
            ->call('resetPassword')
            ->assertNoRedirect();

        $this->assertGuest();
        $this->assertTrue($user->fresh()->hasPendingInvitation());
    }

    public function test_a_wrong_token_is_rejected(): void
    {
        $user = $this->invitedUser();

        Livewire::test(AcceptInvitation::class, ['email' => $user->email, 'token' => 'wrong-token'])
            ->fillForm(['password' => 'my-new-password', 'passwordConfirmation' => 'my-new-password'])
            ->call('resetPassword')
            ->assertNoRedirect();

        $this->assertGuest();
        $this->assertTrue(Hash::check('not-chosen-by-the-user', $user->fresh()->password));
    }

    public function test_admins_can_resend_a_pending_invitation(): void
    {
        Notification::fake();

        $user = $this->invitedUser();

        $this->actingAs(User::factory()->role('admin')->create());

        Livewire::test(ListUsers::class)
            ->callAction(TestAction::make('resendInvitation')->table($user));

        Notification::assertSentTo($user, UserInvitation::class);
    }

    public function test_resend_is_hidden_for_active_users(): void
    {
        $admin = User::factory()->role('admin')->create();

        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->assertActionHidden(TestAction::make('resendInvitation')->table($admin));
    }
}
