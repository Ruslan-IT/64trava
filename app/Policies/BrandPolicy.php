<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;

class BrandPolicy
{
    /**
     * Permanent deletion is restricted to the site owner.
     * Soft delete / restore remain available to all panel admins.
     */
    public function forceDelete(User $user, Brand $brand): bool
    {
        return $user->isOwner();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->isOwner();
    }
}
