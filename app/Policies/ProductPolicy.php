<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Permanent deletion is restricted to the site owner.
     * Soft delete / restore remain available to all panel admins
     * (Filament allows actions without a matching policy method).
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return $user->isOwner();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->isOwner();
    }
}
