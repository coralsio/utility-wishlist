<?php

namespace Corals\Modules\Utility\Wishlist\Providers;

use Corals\Foundation\Providers\BaseUninstallModuleServiceProvider;
use Corals\Modules\Utility\Wishlist\database\migrations\CreateWishlistsTable;
use Corals\Modules\Utility\Wishlist\database\seeds\UtilityWishlistDatabaseSeeder;

class UninstallModuleServiceProvider extends BaseUninstallModuleServiceProvider
{
    protected $migrations = [
        CreateWishlistsTable::class,
    ];

    protected function providerBooted()
    {
        $this->dropSchema();

        $utilityWishlistDatabaseSeeder = new UtilityWishlistDatabaseSeeder();

        $utilityWishlistDatabaseSeeder->rollback();
    }
}
