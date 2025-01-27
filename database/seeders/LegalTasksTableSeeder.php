<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LegalTasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('legal_tasks')->insert([
            [
                'date' => Carbon::now()->subDays(10),
                'task' => 'Review contract terms for Ministry of Health.',
                'ministry' => 'Ministry of Health',
                'legal_advice_meeting' => 'Yes',
                'allocated_date' => Carbon::now()->subDays(8),
                'allocated_to' => 2,
                'status' => 'Pending',
                'onward_action' => 'Draft memo for AG approval.',
                'date_task_achieved' => null,
                'date_approved_by_ag' => null,
                'meeting_date' => Carbon::now()->addDays(5),
                'time_frame' => '1 week',
                'notes' => 'Urgent task requiring immediate attention.',
                'created_by' => 1, // Assumes a user with ID 1 exists
                'updated_by' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'date' => Carbon::now()->subDays(20),
                'task' => 'Provide legal advice on land acquisition.',
                'ministry' => 'Ministry of Lands',
                'legal_advice_meeting' => 'No',
                'allocated_date' => Carbon::now()->subDays(18),
                'allocated_to' => 3,
                'status' => 'Completed',
                'onward_action' => 'Finalize the report.',
                'date_task_achieved' => Carbon::now()->subDays(5),
                'date_approved_by_ag' => Carbon::now()->subDays(2),
                'meeting_date' => null,
                'time_frame' => '2 weeks',
                'notes' => 'Consulted with external legal counsel.',
                'created_by' => 2, // Assumes a user with ID 2 exists
                'updated_by' => 1,
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'date' => Carbon::now()->subDays(30),
                'task' => 'Draft policy on cybersecurity.',
                'ministry' => 'Ministry of Technology',
                'legal_advice_meeting' => 'Yes',
                'allocated_date' => Carbon::now()->subDays(28),
                'allocated_to' => 5,
                'status' => 'In Progress',
                'onward_action' => 'Submit initial draft for review.',
                'date_task_achieved' => null,
                'date_approved_by_ag' => null,
                'meeting_date' => Carbon::now()->addDays(10),
                'time_frame' => '3 weeks',
                'notes' => 'Coordination required with IT department.',
                'created_by' => 3, // Assumes a user with ID 3 exists
                'updated_by' => null,
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(1),
            ],
        ]);
    }
}
