<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // First seed departments and courses
            DepartmentCourseSeeder::class,
            
            // Then seed event types
            EventTypeSeeder::class,
            
            // Finally create admin user
            AdminUserSeeder::class,
        ]);
    }
}