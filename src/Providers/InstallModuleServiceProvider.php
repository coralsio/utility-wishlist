<?php

namespace Corals\Modules\Utility\Wishlist\Providers;

use Corals\Foundation\Providers\BaseInstallModuleServiceProvider;
use Corals\Modules\Utility\Wishlist\database\migrations\CreateWishlistsTable;
use Corals\Modules\Utility\Wishlist\database\seeds\UtilityWishlistDatabaseSeeder;

class InstallModuleServiceProvider extends BaseInstallModuleServiceProvider
{
    protected $migrations = [
        CreateWishlistsTable::class,
    ];

    protected function providerBooted()
    {
        $this->createSchema();

        $utilityWishlistDatabaseSeeder = new UtilityWishlistDatabaseSeeder();

        $utilityWishlistDatabaseSeeder->run();
    }
}
