<?php

namespace App\Policies;

use App\Models\User;

/**
 * Filament hides a resource and blocks its pages when `viewAny` is denied,
 * so this is enough to keep editors out of admin-only resources.
 */
class AdminOnlyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
