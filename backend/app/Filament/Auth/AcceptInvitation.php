<?php

namespace App\Filament\Auth;

use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\PasswordResetResponse;
use Filament\Auth\Pages\PasswordReset\ResetPassword;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * Landing page of the invitation email: the user chooses a password and is logged in.
 *
 * Reuses Filament's reset password form, but validates the token against the
 * `invites` password broker (48 hours) and logs the user in afterwards.
 */
class AcceptInvitation extends ResetPassword
{
	public function resetPassword(): ?PasswordResetResponse
	{
		try {
			$this->rateLimit(5);
		} catch (TooManyRequestsException $exception) {
			$this->getRateLimitedNotification($exception)?->send();

			return null;
		}

		$data = $this->form->getState();
		$data['email'] = $this->email;
		$data['token'] = $this->token;

		$acceptedUser = null;

		$status = Password::broker('invites')->reset(
			$this->getCredentialsFromFormData($data),
			function (User $user) use ($data, &$acceptedUser): void {
				if (! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel())) {
					return;
				}

				$user->forceFill([
					'password' => Hash::make($data['password']),
					'remember_token' => Str::random(60),
					'email_verified_at' => $user->email_verified_at ?? now(),
				])->save();

				event(new PasswordReset($user));

				$acceptedUser = $user;
			}
		);

		if ($status !== Password::PASSWORD_RESET || ! $acceptedUser) {
			Notification::make()
				->title(__('This invitation link is invalid or has expired. Ask an admin to send a new one.'))
				->danger()
				->send();

			return null;
		}

		Filament::auth()->login($acceptedUser);
		session()->regenerate();

		Notification::make()
			->title(__('Welcome! Your account is ready.'))
			->success()
			->send();

		$this->redirect(Filament::getUrl());

		return null;
	}

	public function getTitle(): string | Htmlable
	{
		return __('Accept invitation');
	}

	public function getHeading(): string | Htmlable | null
	{
		return __('Welcome to :app', ['app' => config('app.name')]);
	}

	public function getSubheading(): string | Htmlable | null
	{
		return __('Choose a password to activate your account.');
	}

	public function getResetPasswordFormAction(): Action
	{
		return parent::getResetPasswordFormAction()
			->label(__('Activate account'));
	}
}
