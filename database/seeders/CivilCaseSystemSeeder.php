<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CivilCaseSystemSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks to avoid constraint issues during seeding
        Schema::disableForeignKeyConstraints();

        // Truncate tables to prevent duplicate entries
        DB::table('court_categories')->truncate();
        DB::table('case_types')->truncate();
        DB::table('civil_cases')->truncate();
        DB::table('case_counsels')->truncate();

        // Sample users for created_by, updated_by, plaintiff_id, defendant_id
        $userIds = [1, 2, 3, 4]; // Replace with actual user IDs for plaintiff and defendant

        // Seed Court Categories
        $categories = [
            ['name' => 'High Court', 'code' => 'HC', 'created_by' => $userIds[0], 'updated_by' => $userIds[1]],
            ['name' => 'Magistrate Court', 'code' => 'MC', 'created_by' => $userIds[1], 'updated_by' => $userIds[0]],
        ];
        DB::table('court_categories')->insert($categories);

        // Get the inserted court category IDs
        $courtCategoryIds = DB::table('court_categories')->pluck('id')->toArray();

        // Seed Case Types
$types = [
    ['name' => 'Contract', 'created_by' => $userIds[0], 'updated_by' => $userIds[1]],
    ['name' => 'Land', 'created_by' => $userIds[1], 'updated_by' => $userIds[2]],
    ['name' => 'Tort', 'created_by' => $userIds[2], 'updated_by' => $userIds[0]],
    ['name' => 'Other', 'created_by' => $userIds[0], 'updated_by' => $userIds[1]],
];
        DB::table('case_types')->insert($types);

        // Get the inserted case type IDs
        $caseTypeIds = DB::table('case_types')->pluck('id')->toArray();

        // Seed Civil Cases
        $cases = [
            [
                'court_category_id' => 1, // Assuming 1 is High Court
                'case_type_id' => 1,      // Assuming 1 is Land case
                'primary_number' => 'Lit 4/24',
                'number' => 4,
                'year' => 2024,
                'case_name' => 'Taotin Electronics & Motors Company Limited vs AG iro MISE',
                'case_description' => 'Case involving land dispute between Taotin Electronics and AG',
                'current_status' => 'Pending hearing',
                'status_date' => '2024-01-15',
                'action_required' => 'Prepare defense documents',
                'monitoring_status' => 'Active',
                'entered_by_sg_dsg' => true,
                'created_by' => 1, // Assuming user ID 1 exists
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'court_category_id' => 2, // Assuming 2 is Magistrate Court
                'case_type_id' => 2,      // Assuming 2 is Contract case
                'primary_number' => 'MM 02/20',
                'number' => 2,
                'year' => 2020,
                'case_name' => 'John Doe vs Company XYZ',
                'case_description' => 'Contract dispute case',
                'current_status' => 'Awaiting judgment',
                'status_date' => '2024-01-20',
                'action_required' => 'Follow up with court',
                'monitoring_status' => 'Pending',
                'entered_by_sg_dsg' => false,
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'court_category_id' => 2,
                'case_type_id' => 1,
                'primary_number' => 'Bailan 474/20',
                'number' => 474,
                'year' => 2020,
                'case_name' => 'State vs Property Developer Ltd',
                'case_description' => 'Property development dispute',
                'current_status' => 'Case closed',
                'status_date' => '2024-01-10',
                'action_required' => 'File closing documents',
                'monitoring_status' => 'Closed',
                'entered_by_sg_dsg' => true,
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'court_category_id' => 1,
                'case_type_id' => 3,
                'primary_number' => 'HCCiv 00810/24',
                'number' => 810,
                'year' => 2024,
                'case_name' => 'Corporation ABC vs State Department',
                'case_description' => 'Civil dispute regarding government contract',
                'current_status' => 'Pre-trial conference scheduled',
                'status_date' => '2024-02-01',
                'action_required' => 'Prepare pre-trial documents',
                'monitoring_status' => 'Active',
                'entered_by_sg_dsg' => true,
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];
        // Insert the civil cases into the civil_cases table
        DB::table('civil_cases')->insert($cases);

        // Seed Case Counsel (optional if you have the table and model for case counsel)
        $caseCounsels = [
            [
                'civil_case_id' => 1, // Assuming the first civil case has ID 1
                'user_id' => $userIds[2],
                'type' => 'Plaintiff',
                'created_by' => $userIds[2],
                'updated_by' => $userIds[1],
            ],
            [
                'civil_case_id' => 2, // Assuming the second civil case has ID 2
                'user_id' => $userIds[1],
                'type' => 'Defendant',
                'created_by' => $userIds[1],
                'updated_by' => $userIds[2],
            ],
        ];
        DB::table('case_counsels')->insert($caseCounsels);

        // Enable foreign key checks after seeding
        Schema::enableForeignKeyConstraints();
    }
}
