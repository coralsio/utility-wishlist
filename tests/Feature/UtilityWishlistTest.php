<?php

namespace Tests\Feature;

use Corals\Settings\Facades\Modules;
use Corals\User\Models\User;
use Corals\Utility\Wishlist\Models\Wishlist;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UtilityWishlistTest extends TestCase
{
    use DatabaseTransactions;

    protected $wishlistEcommerce = [];
    protected $wishlist = [];

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $user = User::query()->whereHas('roles', function ($query) {
            $query->where('name', 'member')->whereHas('permissions', function ($queryRole) {
                $queryRole->where('name', 'Utility::my_wishlist.access');
            });
        })->first();
        Auth::loginUsingId($user->id);
    }

    public function test_utility_wishlist_create()
    {
        $modules = [
            'Ecommerce' => ['code' => 'corals-ecommerce', 'prefix' => 'e-commerce/wishlist'],
            'Entity' => ['code' => 'corals-entity', 'prefix' => 'entity/entry/wishlist'],
            'Directory' => ['code' => 'corals-directory', 'prefix' => 'directory/wishlist'],
        ];

        foreach ($modules as $module => $array) {
            if (Modules::isModuleActive($array['code'])) {
                $namespace = 'Corals\Modules\\' . $module . '\\Models';
                $myClasses = array_filter(get_declared_classes(), function ($item) use ($namespace) {
                    return substr($item, 0, strlen($namespace)) === $namespace;
                });

                foreach ($myClasses as $class) {
                    $traits = class_uses($class);
                    if (array_search('Corals\\Utility\\Wishlist\\Traits\\Wishlistable', $traits)) {
                        $model = $class::query()->first();
                        if ($model) {
                            $response = $this->post($array['prefix'] . '/' . $model->hashed_id);
                            $className = substr(strrchr($class, "\\"), 1);

                            $response->assertStatus(200)->assertSeeText($className . ' has been added to wishlist successfully');

                            $this->wishlist = Wishlist::query()->first();

                            $this->assertDatabaseHas('utility_wishlists', [
                                'user_id' => $this->wishlist->user_id,
                                'wishlistable_id' => $this->wishlist->wishlistable_id,
                                'wishlistable_type' => $this->wishlist->wishlistable_type,]);

                            if ($module == 'Ecommerce') {
                                $this->wishlistEcommerce = Wishlist::query()->where('wishlistable_type', '=', 'Corals\Modules\Ecommerce\Models\\' . $className)->first();
                                $this->test_utility_wishlist_delete('Ecommerce', $array['prefix'], $className);
                            }
                        }
                    }
                }
            }
        }

        $this->assertTrue(true);
    }

    public function test_utility_wishlist_Ecommerce_delete($class = null, $prefix = null, $className = null)
    {
        if ($class == 'Ecommerce' && $this->wishlistEcommerce) {
            $response = $this->delete($prefix . '/' . $this->wishlistEcommerce->hashed_id);

            $response->assertStatus(200)->assertSeeText($className . ' has been removed from wishlist successfully');

            $this->isSoftDeletableModel(Wishlist::class);
            $this->assertDatabaseMissing('utility_wishlists', [
                'user_id' => $this->wishlistEcommerce->user_id,
                'wishlistable_id' => $this->wishlistEcommerce->wishlistable_id,
                'wishlistable_type' => $this->wishlistEcommerce->wishlistable_type,]);
        }
        $this->assertTrue(true);
    }

    public function test_utility_wishlist_delete()
    {
        $this->test_utility_wishlist_create();

        $user = User::query()->whereHas('roles', function ($query) {
            $query->where('name', 'superuser');
        })->first();
        Auth::loginUsingId($user->id);

        if ($this->wishlist) {
            $response = $this->delete('utilities/wishlist/' . $this->wishlist->hashed_id);

            $response->assertStatus(200)->assertSeeText('has been removed from wishlist successfully');

            $this->isSoftDeletableModel(Wishlist::class);
            $this->assertDatabaseMissing('utility_wishlists', [
                'user_id' => $this->wishlist->user_id,
                'wishlistable_id' => $this->wishlist->wishlistable_id,
                'wishlistable_type' => $this->wishlist->wishlistable_type,]);
        }
        $this->assertTrue(true);
    }
}
