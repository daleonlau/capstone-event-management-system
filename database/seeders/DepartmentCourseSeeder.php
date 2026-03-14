<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Course;

class DepartmentCourseSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'College of Engineering and Information Technology', 'code' => 'CEIT'],
            ['name' => 'College of Tourism and Hospitality Management', 'code' => 'CTHM'],
            ['name' => 'College of Business and Accountancy', 'code' => 'CBA'],
            ['name' => 'College of Industrial Technology and Teacher Education', 'code' => 'CITTE'],
        ];

        foreach ($departments as $dept) {
            // Use firstOrCreate to avoid duplicates
            $department = Department::firstOrCreate(
                ['code' => $dept['code']],
                ['name' => $dept['name']]
            );
            
            // Add courses for this department (will only run if department was just created)
            $this->addCoursesForDepartment($department);
        }
    }

    private function addCoursesForDepartment($department)
    {
        // Check if courses already exist for this department
        if ($department->courses()->count() > 0) {
            return; // Skip if courses already exist
        }

        switch ($department->code) {
            case 'CEIT':
                Course::insert([
                    ['department_id' => $department->id, 'name' => 'Bachelor of Science in Computer Engineering', 'code' => 'BSCpE', 'created_at' => now(), 'updated_at' => now()],
                    ['department_id' => $department->id, 'name' => 'Bachelor of Science in Information Technology', 'code' => 'BSIT', 'created_at' => now(), 'updated_at' => now()],
                    ['department_id' => $department->id, 'name' => 'Bachelor of Science in Electrical Engineering', 'code' => 'BSEE', 'created_at' => now(), 'updated_at' => now()],
                ]);
                break;
            case 'CTHM':
                Course::insert([
                    ['department_id' => $department->id, 'name' => 'Bachelor of Science in Tourism Management', 'code' => 'BSTM', 'created_at' => now(), 'updated_at' => now()],
                    ['department_id' => $department->id, 'name' => 'Bachelor of Science in Hospitality Management', 'code' => 'BSHM', 'created_at' => now(), 'updated_at' => now()],
                ]);
                break;
            case 'CBA':
                Course::insert([
                    ['department_id' => $department->id, 'name' => 'Bachelor of Science in Entrepreneurship', 'code' => 'BSEnt', 'created_at' => now(), 'updated_at' => now()],
                    ['department_id' => $department->id, 'name' => 'Bachelor of Science in Management Accounting', 'code' => 'BSMA', 'created_at' => now(), 'updated_at' => now()],
                    ['department_id' => $department->id, 'name' => 'Bachelor of Science in Office Administration', 'code' => 'BSOA', 'created_at' => now(), 'updated_at' => now()],
                    ['department_id' => $department->id, 'name' => 'Bachelor of Science in Accountancy', 'code' => 'BSA', 'created_at' => now(), 'updated_at' => now()],
                    ['department_id' => $department->id, 'name' => 'Bachelor of Science in Business Administration', 'code' => 'BSBA', 'created_at' => now(), 'updated_at' => now()],
                ]);
                break;
            case 'CITTE':
                Course::insert([
                    ['department_id' => $department->id, 'name' => 'Bachelor of Science in Industrial Technology', 'code' => 'BSITech', 'created_at' => now(), 'updated_at' => now()],
                    ['department_id' => $department->id, 'name' => 'Bachelor in Technology and Livelihood Education', 'code' => 'BTLEd', 'created_at' => now(), 'updated_at' => now()],
                    ['department_id' => $department->id, 'name' => 'Bachelor of Technology-Vocational Teacher Education', 'code' => 'BTVTE', 'created_at' => now(), 'updated_at' => now()],
                ]);
                break;
        }
    }
}