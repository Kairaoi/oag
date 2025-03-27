<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CriminalJusticeSystemSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Seed users table
        $userIds = [];
        foreach (['Admin User', 'Lawyer 1', 'Lawyer 2', 'Lawyer 3', 'Admin Assistant'] as $name) {
            $userIds[$name] = DB::table('users')->insertGetId([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '', $name)) . '@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $adminUserId = $userIds['Admin User'];
        $lawyerIds = array_slice($userIds, 1, 3);
        $assistantId = $userIds['Admin Assistant'];

        // Seed offence_categories table
        $offenceCategoryIds = [];
        foreach (['Theft', 'Assault', 'Fraud', 'Vandalism', 'Drug Trafficking', 'Arson', 'Burglary', 'Murder'] as $categoryName) {
            $offenceCategoryIds[$categoryName] = DB::table('offence_categories')->insertGetId([
                'category_name' => $categoryName,
                'created_by' => $adminUserId,
                'updated_by' => null,
            ]);
        }

        // Seed islands table
        $islandIds = [];
        foreach (['Island A', 'Island B', 'Island C', 'Island D', 'Island E', 'Island F'] as $islandName) {
            $islandIds[$islandName] = DB::table('islands')->insertGetId([
                'island_name' => $islandName,
                'created_by' => $adminUserId,
                'updated_by' => null,
            ]);
        }

        // Seed reasons_for_closure table
        $closureReasonIds = [];
        foreach (['Lack of evidence', 'Case resolved out of court', 'Witnesses uncooperative', 'Procedural error', 'Insufficient resources', 'Legal technicality'] as $reason) {
            $closureReasonIds[$reason] = DB::table('reasons_for_closure')->insertGetId([
                'reason_description' => $reason,
                'created_by' => $adminUserId,
                'updated_by' => null,
            ]);
        }

        // Seed cases table
        $caseIds = [];
        for ($i = 1; $i <= 100; $i++) {
            $caseIds[] = DB::table('cases')->insertGetId([
                'case_file_number' => 'CASE' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'date_file_received' => now()->subDays($i),
                'case_name' => $faker->sentence(3),
                'date_of_allocation' => now()->subDays($i - 5),
                'date_file_closed' => $i % 2 == 0 ? now()->subDays($i - 10) : null,
                'reason_for_closure_id' => $i % count($closureReasonIds) + 1,
                'lawyer_id' => $lawyerIds[array_rand($lawyerIds)],
                'island_id' => $islandIds[array_rand($islandIds)],
                'created_by' => $adminUserId,
                'updated_by' => null,
            ]);
        }

        // Seed accused table
        for ($i = 1; $i <= 100; $i++) {
            DB::table('accused')->insert([
                'case_id' => $caseIds[$i - 1],
               
               
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'accused_particulars' => 'The accused, ' . $faker->firstName . ' ' . $faker->lastName . ', is alleged to have committed a crime on ' . $faker->date . '. The charge involves theft of property valued at $' . $faker->numberBetween(1000, 10000) . '. The accused has a history of similar offenses and faces potential legal consequences.',
                'gender' => $faker->randomElement(['Male', 'Female']),
                'date_of_birth' => $faker->date,
                'created_by' => $adminUserId,
                'updated_by' => null,
            ]);
        }

        // Seed victims table
        for ($i = 1; $i <= 100; $i++) {
            DB::table('victims')->insert([
                'case_id' => $caseIds[$i - 1],
                'lawyer_id' => $lawyerIds[array_rand($lawyerIds)],
                'island_id' => $islandIds[array_rand($islandIds)],
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'victim_particulars' => 'The victim, ' . $faker->firstName . ' ' . $faker->lastName . ', was involved in an incident on ' . $faker->date . '. The crime resulted in damages estimated at $' . $faker->numberBetween(500, 5000) . '. The victim has cooperated with authorities and provided crucial information about the incident.',
                'gender' => $faker->randomElement(['Male', 'Female']),
                'date_of_birth' => $faker->date,
                'created_by' => $adminUserId,
                'updated_by' => null,
            ]);
        }

        // Seed offences table
        $offenceIds = [];
        foreach (['Petty Theft', 'Grand Theft', 'Physical Assault', 'Corporate Fraud', 'Drug Possession', 'Arson', 'Burglary', 'Homicide'] as $offenceName) {
            $offenceIds[$offenceName] = DB::table('offences')->insertGetId([
                'offence_name' => $offenceName,
                'offence_category_id' => $offenceCategoryIds[array_rand($offenceCategoryIds)],
                'created_by' => $adminUserId,
                'updated_by' => null,
            ]);
        }

        // Seed accused_offence table
        for ($i = 1; $i <= 100; $i++) {
            DB::table('accused_offence')->insert([
                'accused_id' => $i,
                'offence_id' => $offenceIds[array_rand($offenceIds)],
            ]);
        }

        // Seed incidents table
        for ($i = 1; $i <= 100; $i++) {
            DB::table('incidents')->insert([
                'case_id' => $caseIds[$i - 1],
                'lawyer_id' => $lawyerIds[array_rand($lawyerIds)],
                'island_id' => $islandIds[array_rand($islandIds)],
                'date_of_incident_start' => now()->subDays($i + 5),
                'date_of_incident_end' => now()->subDays($i + 3),
                'place_of_incident' => $faker->city,
                'created_by' => $adminUserId,
                'updated_by' => null,
            ]);
        }
    }
}
