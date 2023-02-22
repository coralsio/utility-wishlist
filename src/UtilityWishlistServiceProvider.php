<?php

namespace Corals\Modules\Utility\Wishlist;

use Corals\Foundation\Providers\BasePackageServiceProvider;
use Corals\Modules\Utility\Wishlist\Classes\WishlistManager;
use Corals\Modules\Utility\Wishlist\Models\Wishlist;
use Corals\Modules\Utility\Wishlist\Providers\UtilityAuthServiceProvider;
use Corals\Modules\Utility\Wishlist\Providers\UtilityRouteServiceProvider;
use Corals\Settings\Facades\Modules;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\AliasLoader;

class UtilityWishlistServiceProvider extends BasePackageServiceProvider
{
    /**
     * @var
     */
    protected $packageCode = 'corals-utility-wishlist';

    public function bootPackage()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'utility-wishlist');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'utility-wishlist');

        $this->mergeConfigFrom(
            __DIR__ . '/config/utility-wishlist.php',
            'utility-wishlist'
        );
        $this->publishes([
            __DIR__ . '/config/utility-wishlist.php' => config_path('utility-wishlist.php'),
            __DIR__ . '/resources/views' => resource_path('resources/views/vendor/utility-wishlist'),
        ]);

        $this->registerMorphMaps();
    }

    public function registerPackage()
    {
        $this->app->register(UtilityAuthServiceProvider::class);
        $this->app->register(UtilityRouteServiceProvider::class);

        $this->app->booted(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('WishlistManager', WishlistManager::class);
        });
    }

    protected function registerMorphMaps()
    {
        Relation::morphMap([
            'UtilityWishlist' => Wishlist::class,
        ]);
    }

    public function registerModulesPackages()
    {
        Modules::addModulesPackages('corals/utility-wishlist');
    }
}
