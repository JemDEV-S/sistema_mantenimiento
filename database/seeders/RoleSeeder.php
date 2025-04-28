<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'Administrator',
                'description' => 'System administrator with full access',
            ],
            [
                'name' => 'Technician',
                'description' => 'IT technician responsible for maintenance',
            ],
            [
                'name' => 'Manager',
                'description' => 'Department manager',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
