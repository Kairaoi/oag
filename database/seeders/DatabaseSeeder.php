<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
{
    $this->call(CriminalJusticeSystemSeeder::class);
    $this->call(CivilCaseSystemSeeder::class);
    
    $this->call(CourtAttendanceSeeder::class);
    $this->call(LegalTasksTableSeeder::class);
    $this->call(LegalSystemSeeder::class);
    $this->call(CourtsTableSeeder::class);
    $this->call(ReportSeeder::class);
    $this->call([
        CivilLitigationSeeder::class,
    ]);
}
}
