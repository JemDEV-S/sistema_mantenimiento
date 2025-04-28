<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = [
            [
                'name' => 'IT Department',
                'code' => 'IT-DEPT',
                'location' => 'First Floor',
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR-DEPT',
                'location' => 'Second Floor',
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN-DEPT',
                'location' => 'Third Floor',
            ],
            [
                'name' => 'Marketing',
                'code' => 'MKT-DEPT',
                'location' => 'Fourth Floor',
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS-DEPT',
                'location' => 'Fifth Floor',
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
