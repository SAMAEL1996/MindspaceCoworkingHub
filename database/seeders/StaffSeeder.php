<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $code = \App\Models\Card::where('type', 'Staff')->first();
        $role = Role::create(['name' => 'Staff']);

        $staffUser = \App\Models\User::create([
            'name' => 'Test Staff',
            'email' => 'teststaff@mindspace.com',
            'password' => bcrypt('ilovemindspace'),
            'is_staff' => true
        ]);
        $staffUser->staff()->create([
            'card_id' => $code->id,
            'personal_email' => 'personalemail@gmail.com',
            'is_active' => true,
            'emergency_contact_person' => 'test',
            'emergency_relationship' => 'test',
            'emergency_contact_no' => '09999999999',
        ]);
        $staffUser->assignRole($role);
    }
}
