<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CivilLitigationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lookup tables
        DB::table('causes_of_action')->insert([
            ['name' => 'Land', 'description' => 'Failure to honor contract terms'],
            ['name' => 'Tort', 'description' => 'Failure to exercise reasonable care'],
            ['name' => 'Contract', 'description' => 'Failure to exercise reasonable care'],
            ['name' => 'Other', 'description' => 'Failure to exercise reasonable care'],
        ]);

//         DB::table('case_statuses')->insert([
//     [
//         'case_id' => 1, // Make sure the case with ID 1 exists
//         'status_date' => now(),
//         'current_status' => 'OPEN - Pending with Court',
//         'action_required' => 'Case is active and awaiting court action',
//         'monitoring_status' => null,
//         'created_by' => 1,
//         'updated_by' => 1,
//         'created_at' => now(),
//         'updated_at' => now(),
//     ],
//     // More statuses for different cases
// ]);

        

        // DB::table('case_pending_statuses')->insert([
        //     ['name' => 'Awaiting Filing', 'description' => 'Filing not yet done'],
        //     ['name' => 'Pending Review', 'description' => 'Under legal review'],
        // ]);

        DB::table('party_types')->insert([
            ['name' => 'Plaintiff', 'description' => 'Initiating party'],
            ['name' => 'Defendant', 'description' => 'Responding party'],
        ]);

        DB::table('case_origin_types')->insert([
            ['name' => 'Government is being sued', 'description' => 'Case filed against the government, typically referred from another agency'],
            ['name' => 'Government is suing', 'description' => 'Case initiated by the government as the complainant, filed directly'],
        ]);
        

        // 2. Main table: civil_cases
        // DB::table('civil2_cases')->insert([
        //     [
        //         'case_file_no' => 'CIV2025/001',
        //         'court_case_no' => 'HC/2025/22',
        //         'case_name' => 'Attorney General vs XYZ Ltd',
        //         'date_received' => now(),
        //         'date_opened' => now(),
        //         'cause_of_action_id' => 1,
        //         'responsible_counsel_id' => 1, // assuming user ID 1 exists
        //         // 'case_status_id' => 1,
        //         'court_type_id' => 1,
        //         'case_pending_status_id' => 1,
        //         'case_origin_type_id' => 2,
        //         'case_description' => 'Alleged violation of procurement laws',
        //         'created_by' => 1,
        //         'updated_by' => 1,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        // ]);

        // 3. Dependent tables
        DB::table('case_parties')->insert([
            // Represented by a government lawyer (user ID = 1)
            [
                'case_id' => 1,
                'party_name' => 'XYZ Ltd',
                'party_type_id' => 2, // Defendant
                'is_represented' => true,
                'represented_by_user_id' => 1,
                'represented_by_external' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        
            // Represented by an external lawyer
            [
                'case_id' => 1,
                'party_name' => 'John Doe',
                'party_type_id' => 1, // Plaintiff
                'is_represented' => true,
                'represented_by_user_id' => null,
                'represented_by_external' => 'Adv. Jane Smith',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        
            // No representation
            [
                'case_id' => 1,
                'party_name' => 'ABC Corp',
                'party_type_id' => 2, // Defendant
                'is_represented' => false,
                'represented_by_user_id' => null,
                'represented_by_external' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        

        // DB::table('case_status_history')->insert([
        //     [
        //         'case_id' => 1,
        //         'case_status_id' => 1,
        //         'case_pending_status_id' => 1,
        //         'updated_by' => 1,
        //         'notes' => 'Initial status set at case intake',
        //         'created_by' => 1,
        //         'updated_by' => 1,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        // ]);

        DB::table('case_activities')->insert([
            [
                'case_id' => 1,
                'activity_type' => 'Court Filing',
                'activity_date' => now(),
                'description' => 'Filed originating summons in High Court',
                'performed_by' => 1,
                'document_reference' => 'DOC001',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('case_closures')->insert([
            [
                'case_id' => 1,
                'memo_date' => now(),
                'sg_clearance' => false,
                'ag_endorsement' => false,
                'file_archived' => false,
                'closed_by' => 2, // assuming user ID 2 exists
                'closure_notes' => 'Case ongoing, closure initiated prematurely',
                'created_by' => 2,
                'updated_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
