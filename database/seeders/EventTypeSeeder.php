<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventType;
use Illuminate\Support\Facades\DB;

class EventTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate the table
        DB::table('event_types')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert event types
        DB::table('event_types')->insert([
            [
                'name' => 'Payment Event',
                'slug' => 'payment-event',
                'requires_payment' => true,
                'requires_document' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Assembly',
                'slug' => 'assembly',
                'requires_payment' => false,
                'requires_document' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Celebration Event',
                'slug' => 'celebration-event',
                'requires_payment' => false,
                'requires_document' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}