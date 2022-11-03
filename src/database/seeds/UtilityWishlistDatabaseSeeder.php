<?php

namespace Corals\Modules\Utility\Wishlist\database\seeds;

use Corals\User\Models\Permission;
use Illuminate\Database\Seeder;

class UtilityWishlistDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UtilityWishlistPermissionsDatabaseSeeder::class);
    }

    public function rollback()
    {
        Permission::where('name', 'like', 'Utility::wishlist%')->delete();
        Permission::where('name', 'Administrations::admin.utility_wishlist')->delete();
    }
}
