<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionName = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view staff',
            'create staff',
            'edit staff',
            'delete staff',
            'view cards',
            'create cards',
            'edit cards',
            'delete cards',
            'view daily-sales',
            'create daily-sales',
            'edit daily-sales',
            'delete daily-sales',
            'generate daily-sales',
            'export daily-sales',
            'view monthly-users',
            'create monthly-users',
            'edit monthly-users',
            'delete monthly-users',
            'view flexi-users',
            'create flexi-users',
            'edit flexi-users',
            'delete flexi-users',
            'view reports',
            'create reports',
            'edit reports',
            'delete reports',
            'export reports',
            'view sale-reports',
            'create sale-reports',
            'edit sale-reports',
            'delete sale-reports',
            'export sale-reports',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view attendances',
            'export attendances',
            'view profiles',
            'create profiles',
            'edit profiles',
            'delete profiles',
            'view staff-profile'
        ];

        foreach($permissionName as $name) {
            Permission::create(['name' => $name]);
        }
    }
}
