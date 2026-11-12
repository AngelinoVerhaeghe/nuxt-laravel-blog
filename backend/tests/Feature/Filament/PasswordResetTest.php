<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Filament\Auth\Notifications\ResetPassword;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Filament\Auth\Pages\PasswordReset\ResetPassword as ResetPasswordPage;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_login_page_links_to_the_password_reset(): void
    {
        $url = Filament::getPanel('dashboard')->getRequestPasswordResetUrl();

        $this->assertNotNull($url);
        $this->get('/dashboard/login')
            ->assertOk()
            ->assertSee($url, escape: false);
    }

    public function test_a_reset_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->role('editor')->create();

        Livewire::test(RequestPasswordReset::class)
            ->fillForm(['email' => $user->email])
            ->call('request')
            ->assertHasNoFormErrors();

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) {
            return str_starts_with($notification->url, url('/dashboard/password-reset/reset'));
        });
    }

    public function test_the_password_can_be_reset_with_the_link(): void
    {
        $user = User::factory()->role('editor')->create();
        $token = Password::broker()->createToken($user);

        Livewire::test(ResetPasswordPage::class, ['email' => $user->email, 'token' => $token])
            ->fillForm([
                'password' => 'a-brand-new-password',
                'passwordConfirmation' => 'a-brand-new-password',
            ])
            ->call('resetPassword')
            ->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('a-brand-new-password', $user->fresh()->password));
    }

    public function test_a_wrong_token_does_not_reset_the_password(): void
    {
        $user = User::factory()->role('editor')->create(['password' => 'the-old-password']);

        Livewire::test(ResetPasswordPage::class, ['email' => $user->email, 'token' => 'wrong-token'])
            ->fillForm([
                'password' => 'a-brand-new-password',
                'passwordConfirmation' => 'a-brand-new-password',
            ])
            ->call('resetPassword');

        $this->assertTrue(Hash::check('the-old-password', $user->fresh()->password));
    }
}
