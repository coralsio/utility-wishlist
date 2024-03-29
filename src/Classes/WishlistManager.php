<?php

namespace Corals\Utility\Wishlist\Classes;

use Corals\Utility\Wishlist\Models\Wishlist as WishlistModel;
use Illuminate\Database\Eloquent\Model;

class WishlistManager
{
    protected $instance;
    protected $user;

    /**
     * WishlistManager constructor.
     * @param $instance
     * @param null $user
     */
    public function __construct($instance, $user = null)
    {
        $this->instance = $instance;

        $this->user = is_null($user) ? user() : $user;
    }

    /**
     * @return WishlistModel|Model
     */
    public function createWishlistItem()
    {
        $data = [
            'wishlistable_id' => $this->instance->id,
            'wishlistable_type' => getMorphAlias($this->instance),
            'user_id' => $this->user->id,
        ];

        return WishlistModel::create($data);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function handleWishlistItem()
    {
        $wishlistItem = $this->getWishlistItem(false);

        if ($wishlistItem) {
            $this->deleteItem($wishlistItem);
            $state = 'delete';
        } else {
            $state = 'add';
            $this->createWishlistItem();
        }

        return $state;
    }

    /**
     * @param WishlistModel $wishlist
     * @return bool|null
     * @throws \Exception
     */
    public function deleteItem(WishlistModel $wishlist)
    {
        return $wishlist->delete();
    }

    /**
     * @param bool $count
     * @return \Illuminate\Database\Eloquent\Builder|WishlistModel|int|null|object
     */
    public function getWishlistItem($count = true)
    {
        $wishlistQB = WishlistModel::query()
            ->where('wishlistable_id', $this->instance->id)
            ->where('wishlistable_type', getMorphAlias($this->instance))
            ->where('user_id', $this->user->id);

        if ($count) {
            return $wishlistQB->count();
        } else {
            return $wishlistQB->first();
        }
    }

    public function getUserWishlist($count = false)
    {
        $wishlistsQB = WishlistModel::query()
            ->where('wishlistable_type', getMorphAlias($this->instance))
            ->where('user_id', $this->user->id);

        if ($count) {
            return $wishlistsQB->count();
        } else {
            return $wishlistsQB;
        }
    }
}
