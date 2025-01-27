<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Oag\Crime\ReportGroup;
use App\Models\Oag\Crime\Report;
use Illuminate\Support\Facades\DB;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks to truncate the tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Seed report groups if they don't exist
        $reportGroup = ReportGroup::where('name', 'Crime Reports')->first();
        if (!$reportGroup) {
            $reportGroup = ReportGroup::create([
                'name' => 'Crime Reports',
                'description' => 'Reports related to Criminal Records',
            ]);
        }

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Seed reports if they don't exist
        $reports = [
            [
                'report_group_id' => $reportGroup->id,
                'name' => 'Cases Overview',
                'description' => 'The Cases Overview report summarizes case details and status.',
                'query' => '
                    SELECT
                        c.id AS case_id,
                        c.case_file_number,
                        c.date_file_received,
                        c.case_name,
                        c.date_of_allocation,
                        c.date_file_closed,
                        r.reason_description AS reason_for_closure,
                        l.name AS lawyer_name,
                        i.island_name
                    FROM cases c
                    JOIN reasons_for_closure r ON c.reason_for_closure_id = r.id
                    JOIN users l ON c.lawyer_id = l.id
                    JOIN islands i ON c.island_id = i.id
                    ORDER BY c.date_file_received DESC;',
            ],

            [
                'report_group_id' => $reportGroup->id,
                'name' => 'Incidents',
                'description' => 'List All Incidents with Associated Case and Lawyer.',
                'query' => '
                    SELECT 
                        incidents.id AS incident_id,
                        incidents.date_of_incident_start,
                        incidents.date_of_incident_end,
                        incidents.place_of_incident,
                        cases.case_name,
                        users.name AS lawyer_name,
                        islands.island_name
                    FROM incidents
                    INNER JOIN cases ON incidents.case_id = cases.id
                    INNER JOIN users ON incidents.lawyer_id = users.id
                    INNER JOIN islands ON incidents.island_id = islands.id
                    WHERE incidents.deleted_at IS NULL;
                    ',
            ],
        ];

        foreach ($reports as $reportData) {
            $existingReport = Report::where([
                'report_group_id' => $reportData['report_group_id'],
                'name' => $reportData['name'],
            ])->first();

            if (!$existingReport) {
                Report::create($reportData);
            }
        }
    }
}
