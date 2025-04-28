<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'position' => 'System Administrator',
            'role_id' => 1, // Administrator
            'department_id' => 1, // IT Department
        ]);

        // Create technicians
        $technicians = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'position' => 'IT Support Technician',
                'role_id' => 2, // Technician
                'department_id' => 1, // IT Department
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'position' => 'IT Support Specialist',
                'role_id' => 2, // Technician
                'department_id' => 1, // IT Department
            ],
        ];

        foreach ($technicians as $technician) {
            User::create($technician);
        }

        // Create department managers
        $departments = Department::all();
        
        foreach ($departments as $index => $department) {
            $manager = User::create([
                'name' => 'Manager ' . $department->name,
                'email' => 'manager_' . $department->code . '@example.com',
                'password' => Hash::make('password'),
                'position' => $department->name . ' Manager',
                'role_id' => 3, // Manager
                'department_id' => $department->id,
            ]);

            // Update department with manager
            $department->manager_id = $manager->id;
            $department->save();
        }
    }
}
