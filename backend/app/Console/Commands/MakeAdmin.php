<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeAdmin extends Command
{
    protected $signature = 'app:make-admin
                            {email : Email address of the admin}
                            {--name= : Name for a new user (defaults to the part before the @)}';

    protected $description = 'Give a user the admin role, or invite a new admin by email';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->assignRole('admin');

            $this->components->info("{$email} is now an admin.");

            return self::SUCCESS;
        }

        $user = User::create([
            'name' => $this->option('name') ?: Str::before($email, '@'),
            'email' => $email,
            // The user chooses their own password through the invitation link
            'password' => Str::random(64),
        ]);

        $user->assignRole('admin');
        $user->sendInvitation();

        $this->components->info("Created admin {$email} and sent an invitation to set a password.");

        return self::SUCCESS;
    }
}
