<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CourtsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // You can optionally create users first if they don't exist.
        // Example: Creating a test user (if no users exist)
        $user = User::first(); // Assuming you already have users in the database.
        
        // Seed some sample courts
        DB::table('courts')->insert([
            [
                'court_name' => 'Supreme Court',
                'description' => 'The highest court in the country.',
                'created_by' => $user->id, // Assuming $user is the creator
                'updated_by' => $user->id, // Assuming $user is the updater
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'court_name' => 'High Court',
                'description' => 'A court of law having jurisdiction over civil and criminal cases.',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'court_name' => 'District Court',
                'description' => 'A court that deals with civil and criminal cases at the district level.',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
