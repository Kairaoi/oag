<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon; 


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
foreach ([
    'Tarawa', 'Abaiang', 'Abemama', 'Aranuka', 'Arorae', 'Banaba', 'Beru', 'Butaritari', 'Kanton',
    'Kiritimati', 'Kuria', 'Maiana', 'Makin', 'Marakei', 'Nikunau', 'Nonouti', 'Onotoa', 'Tabiteuea',
    'Tamana', 'Tabuaeran', 'Teraina'
] as $islandName) {
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

   
       // Seed cases table (ensuring date_of_incident is not null)
$caseIds = [];
for ($i = 1; $i <= 2; $i++) {
    $caseIds[] = DB::table('cases')->insertGetId([
        'case_file_number' => 'CASE' . str_pad($i, 3, '0', STR_PAD_LEFT),
        'date_file_received' => now()->subDays($i),
        'case_name' => 'Republic vs ' . $faker->firstName . ' ' . $faker->lastName,
        'date_of_incident' => $faker->dateTimeBetween('-5 days', 'now')->format('Y-m-d'), // ✅ always set
        'lawyer_id' => $faker->randomElement(array_values($lawyerIds)),
        'island_id' => $faker->randomElement(array_values($islandIds)),
        'created_by' => $adminUserId,
        'updated_by' => null,
        'deleted_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

        


for ($i = 1; $i <= 2; $i++) {
    $dob = $faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d');
    $age = Carbon::parse($dob)->age;

    DB::table('accused')->insert([
        'case_id' => $caseIds[array_rand($caseIds)],
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'address' => $faker->address,
        'contact' => $faker->email,
        'phone' => $faker->phoneNumber,
        'gender' => $faker->randomElement(['Male', 'Female', 'Other']),
        'age' => $age,
        'date_of_birth' => $dob,
        'island_id' => $islandIds[array_rand($islandIds)],
        'created_by' => $adminUserId,
        'updated_by' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
       // Seed victims table
for ($i = 1; $i <= 2; $i++) {
    $firstName = $faker->firstName;
    $lastName = $faker->lastName;
    $dob = $faker->date('Y-m-d', '2010-01-01'); // safer date range for age logic
    $age = Carbon::parse($dob)->age;
    // Determine age group
    if ($age < 13) {
        $ageGroup = 'Under 13';
    } elseif ($age < 15) {
        $ageGroup = 'Under 15';
    } elseif ($age < 18) {
        $ageGroup = 'Under 18';
    } else {
        $ageGroup = 'Above 18';
    }

    DB::table('victims')->insert([
        'case_id'       => $caseIds[$i - 1],
        'island_id'     => $islandIds[array_rand($islandIds)],
        'first_name'    => $firstName,
        'last_name'     => $lastName,
        'address'       => $faker->address,
        'contact'       => $faker->email,
        'phone'         => $faker->phoneNumber,
        'gender'        => $faker->randomElement(['Male', 'Female', 'Other']),
        'age'           => $age,
        'date_of_birth' => $dob,
        'age_group'     => $ageGroup,
        'created_by'    => $adminUserId,
        'updated_by'    => null,
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

        // // Seed accused_offence table
        for ($i = 1; $i <= 2; $i++) {
            DB::table('accused_offence')->insert([
                'accused_id' => $i,
                'offence_id' => $offenceIds[array_rand($offenceIds)],
            ]);
        }

        // // Seed incidents table
        for ($i = 1; $i <= 2; $i++) {
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

        //  // Insert court cases
         for ($i = 1; $i <= 2; $i++) {
            DB::table('court_cases')->insert([
                'case_id' => $faker->randomElement($caseIds),
                'charge_file_dated' => $faker->date(),
                'high_court_case_number' => $faker->optional()->word,
                'verdict' => $faker->randomElement(['guilty', 'not_guilty', 'dismissed', 'withdrawn', 'other']),
                             
                'judgment_delivered_date' => $faker->optional()->date(),
                'court_outcome' => $faker->randomElement(['win', 'lose']),
                'decision_principle_established' => $faker->optional()->text,
                'created_by' => $faker->randomElement($userIds),
                'updated_by' => $faker->randomElement($userIds),
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
