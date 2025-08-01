<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed basic data - students will be imported from CSV
        $this->call([
            SubjectSeeder::class,
            PrerequisitesSeeder::class,
            // StudentSeeder::class, // Commented out - students are imported from CSV
            // StudentCurrentSubjectSeeder::class, // Commented out - will be created during CSV import
        ]);
    }
}
