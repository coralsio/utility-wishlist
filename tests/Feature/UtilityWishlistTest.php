<?php

namespace Tests\Feature;

use Corals\Modules\Utility\Wishlist\Models\Wishlist;
use Corals\Settings\Facades\Modules;
use Corals\User\Models\User;
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
                    if (array_search('Corals\\Modules\\Utility\\Wishlist\\Traits\\Wishlistable', $traits)) {
                        $model = $class::query()->first();
                        if ($model) {
                            $response = $this->post($array['prefix'] . '/' . $model->hashed_id);
                            $className = substr(strrchr($class, "\\"), 1);

                            $response->assertStatus(200)->assertSeeText($className . ' has been added to wishlist successfully');

                            if ($module == 'Ecommerce') {
                                $this->wishlistEcommerce = Wishlist::query()->where('wishlistable_type', '=', 'Corals\Modules\Ecommerce\Models\\' . $className)->first();
                                $this->test_utility_wishlist_delete('Ecommerce', $array['prefix'], $className);
                            }
                        }
                    }
                }
            }
        }
        $this->wishlist = Wishlist::query()->first();

        $this->assertTrue(true);
    }


    public function test_utility_wishlist_Ecommerce_delete($class = null, $prefix = null, $className = null)
    {
        if ($class == 'Ecommerce' && $this->wishlistEcommerce) {
            $response = $this->delete($prefix . '/' . $this->wishlistEcommerce->hashed_id);

            $response->assertStatus(200)->assertSeeText($className . ' has been removed from wishlist successfully');
        }
        $this->assertTrue(true);
    }

    public function test_utility_wishlist_delete()
    {
        if ($this->wishlist) {
            $response = $this->delete('wishlist/' . $this->wishlist->hashed_id);

            $response->assertStatus(200)->assertSeeText('has been removed from wishlist successfully');
        }
        $this->assertTrue(true);
    }
}