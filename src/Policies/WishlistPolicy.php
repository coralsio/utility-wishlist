<?php

namespace Corals\Utility\Wishlist\Policies;

use Corals\Utility\Wishlist\Models\Wishlist;
use Corals\User\Models\User;

class WishlistPolicy
{
    public function destroy(User $user, Wishlist $wishlist)
    {
        return $wishlist->user_id == $user->id;
    }

    /**
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before($user, $ability)
    {
        if ($user->hasPermissionTo('Administrations::admin.utility')) {
            return true;
        }

        return null;
    }
}
