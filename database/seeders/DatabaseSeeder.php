<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $role = Role::create(['name' => 'Super Administrator']);

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@mindspace.com',
            'password' => bcrypt('P@$$w07d'),
        ]);
        $admin->assignRole($role);

        $this->call([
            PermissionSeeder::class,
            CardSeeder::class,
            StaffSeeder::class,
        ]);
    }
}
