<?php

namespace Corals\Modules\Utility\Wishlist;

use Corals\Modules\Utility\Wishlist\Providers\UtilityAuthServiceProvider;
use Corals\Modules\Utility\Wishlist\Providers\UtilityRouteServiceProvider;
use Corals\Modules\Utility\Wishlist\Classes\WishlistManager;
use Corals\Modules\Utility\Wishlist\Models\Wishlist;
use Corals\Settings\Facades\Modules;
use Corals\User\Communication\Facades\CoralsNotification;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class UtilityWishlistServiceProvider extends ServiceProvider
{
    public function boot()
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
        $this->registerModulesPackages();

    }

    public function register()
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

    protected function registerModulesPackages()
    {
        Modules::addModulesPackages('corals/utility-wishlist');
    }
}
