<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\UserInvitation;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasAvatar, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

	public function getFilamentAvatarUrl(): ?string
	{
		return $this->avatar_url;
	}

	public function posts(): HasMany
	{
		return $this->hasMany(Post::class);
	}

	public function canAccessPanel( Panel $panel ): bool
	{
		return $this->hasAnyRole(['admin', 'editor']);
	}

	/**
	 * An invited user hasn't set a password yet; accepting the invitation verifies the email.
	 */
	public function hasPendingInvitation(): bool
	{
		return $this->email_verified_at === null;
	}

	/**
	 * Email a one-time link where the user sets a password and gets logged in.
	 */
	public function sendInvitation(): void
	{
		$token = Password::broker('invites')->createToken($this);

		$url = URL::temporarySignedRoute(
			'filament.dashboard.auth.invitation.accept',
			now()->addMinutes(config('auth.passwords.invites.expire')),
			['email' => $this->getEmailForPasswordReset(), 'token' => $token],
		);

		$this->notify(new UserInvitation($url));
	}
}
