<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportGroup;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run()
    {
        try {
            // Delete existing reports in this group
            Report::whereHas('reportGroup', function ($query) {
                $query->where('name', 'Criminal Justice Reports');
            })->delete();

            ReportGroup::where('name', 'Criminal Justice Reports')->delete();

            $group = ReportGroup::create([
                'name' => 'Criminal Justice Reports',
                'description' => 'Key metrics and summaries for the justice system'
            ]);

            // Report 1 - Total Cases by Status
            Report::create([
                'name' => 'Total Cases by Status',
                'description' => 'Summary of criminal cases grouped by status.',
                'query' => "
                    SELECT 
                        status,
                        COUNT(*) as total_cases
                    FROM criminal_cases
                    GROUP BY status;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $group->id
            ]);

            // Report 2 - Court Cases by Outcome
            Report::create([
                'name' => 'Court Cases by Outcome',
                'description' => 'Distribution of court case outcomes.',
                'query' => "
                    SELECT 
                        outcome,
                        COUNT(*) as total
                    FROM court_cases
                    GROUP BY outcome;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $group->id
            ]);

            // Report 3 - Accused Per Case
            Report::create([
                'name' => 'Average Accused Per Case',
                'description' => 'Average number of accused persons per case.',
                'query' => "
                    SELECT 
                        AVG(accused_count) as avg_accused_per_case
                    FROM (
                        SELECT 
                            criminal_case_id, 
                            COUNT(*) as accused_count
                        FROM accused
                        GROUP BY criminal_case_id
                    ) as sub;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $group->id
            ]);

            // Report 4 - Case Allocation Summary
            Report::create([
                'name' => 'Case Allocation Summary',
                'description' => 'Number of cases assigned to each lawyer.',
                'query' => "
                    SELECT 
                        l.name as lawyer_name,
                        COUNT(c.id) as total_cases
                    FROM criminal_cases c
                    JOIN lawyers l ON c.lawyer_id = l.id
                    GROUP BY l.name;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $group->id
            ]);

            // Report 5 - Monthly Case Filing
            Report::create([
                'name' => 'Monthly Case Filing',
                'description' => 'Number of cases filed each month.',
                'query' => "
                    SELECT 
                        DATE_FORMAT(created_at, '%Y-%m') as month,
                        COUNT(*) as total_filed
                    FROM criminal_cases
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                    ORDER BY month;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $group->id
            ]);

            $this->command->info('Criminal Justice Reports seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding Reports: ' . $e->getMessage());
        }
    }
}
