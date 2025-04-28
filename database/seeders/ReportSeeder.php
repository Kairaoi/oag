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
                        cr.date_file_closed,  -- From case_reviews table
                        r.reason_description AS reason_for_closure,  -- From case_reviews table
                        l.name AS lawyer_name,
                        i.island_name
                    FROM cases c
                    JOIN users l ON c.lawyer_id = l.id
                    JOIN islands i ON c.island_id = i.id
                    LEFT JOIN case_reviews cr ON c.id = cr.case_id  -- Join with case_reviews for the closed date and reason
                    LEFT JOIN reasons_for_closure r ON cr.reason_for_closure_id = r.id  -- Join reason for closure from case_reviews
                    ORDER BY c.date_file_received DESC;
                ',
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

            [
                'report_group_id' => $reportGroup->id,
                'name' => 'Active Cases',
                'description' => 'List of Active Cases with Lawyer, Island, and Status.',
                'query' => '
                    SELECT
                        c.id AS case_id,
                        c.case_file_number,
                        c.case_name,
                        c.date_file_received,
                        l.name AS lawyer_name,
                        i.island_name,
                        c.status
                    FROM cases c
                    JOIN users l ON c.lawyer_id = l.id
                    JOIN islands i ON c.island_id = i.id
                    WHERE c.deleted_at IS NULL
                    ORDER BY c.date_file_received DESC;
                ',
            ],

            [
                'report_group_id' => $reportGroup->id,
                'name' => 'Offences and Categories',
                'description' => 'List of Offences and their Categories.',
                'query' => '
                    SELECT 
                        o.id AS offence_id,
                        o.offence_name,
                        oc.category_name
                    FROM offences o
                    JOIN offence_categories oc ON o.offence_category_id = oc.id
                    WHERE o.deleted_at IS NULL
                    ORDER BY o.offence_name ASC;
                ',
            ],

            [
                'report_group_id' => $reportGroup->id,
                'name' => 'Victims List per Case',
                'description' => 'List of Victims for Each Case.',
                'query' => '
                    SELECT
                        v.id AS victim_id,
                        c.case_file_number,
                        CONCAT(v.first_name, " ", v.last_name) AS victim_name,
                        v.gender,
                        v.age_group,
                        v.date_of_birth,
                        i.island_name
                    FROM victims v
                    JOIN cases c ON v.case_id = c.id
                    JOIN islands i ON v.island_id = i.id
                    WHERE v.deleted_at IS NULL
                    ORDER BY v.created_at DESC;
                ',
            ],

            [
                'report_group_id' => $reportGroup->id,
                'name' => 'Accused Persons per Case',
                'description' => 'List of Accused Persons for Each Case.',
                'query' => '
                    SELECT
                        a.id AS accused_id,
                        c.case_file_number,
                        CONCAT(a.first_name, " ", a.last_name) AS accused_name,
                        a.gender,
                        a.age,
                        a.date_of_birth,
                        i.island_name
                    FROM accused a
                    JOIN cases c ON a.case_id = c.id
                    JOIN islands i ON a.island_id = i.id
                    WHERE a.deleted_at IS NULL
                    ORDER BY a.created_at DESC;
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
