<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Oag\Civil\CourtAttendance;
use Carbon\Carbon;

class CourtAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Example data for court attendance records
        CourtAttendance::insert([
            [
                'civil_case_id' => 1,  // Replace with an existing civil case ID
                'opposing_counsel_name' => 1,
                'hearing_date' => Carbon::parse('2024-10-15'),
                'hearing_type' => 'Hearing',
                'hearing_time' => '10:00:00',
                'case_status' => 'Ongoing',
                'status_notes' => 'Initial hearing phase',
                'created_by' => 1, // Replace with an existing user ID
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'civil_case_id' => 2,
                'opposing_counsel_name' => 2,
                'hearing_date' => Carbon::parse('2024-11-20'),
                'hearing_type' => 'Mention',
                'hearing_time' => '14:30:00',
                'case_status' => 'Adjourned',
                'status_notes' => 'Case adjourned to a later date',
                'created_by' => 2,
                'updated_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'civil_case_id' => 3,
                'opposing_counsel_name' => 3,
                'hearing_date' => Carbon::parse('2024-12-05'),
                'hearing_type' => 'Concluded',
                'hearing_time' => '09:15:00',
                'case_status' => 'Concluded',
                'status_notes' => 'Final judgment passed',
                'created_by' => 3,
                'updated_by' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
