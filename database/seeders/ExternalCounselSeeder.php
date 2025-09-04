<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExternalCounselSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('external_counsels')->insert([
            [
                'name' => 'Banuera Berina',
                'email' => 'b.berina@legalmail.com',
                'phone' => '72010001',
                'address' => 'Betio, South Tarawa',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Taoing Matera',
                'email' => 'taoing.matera@lawyers.org',
                'phone' => '72010002',
                'address' => 'Bairiki, South Tarawa',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Kiaro Tieka',
                'email' => 'kiaro.tieka@advocates.net',
                'phone' => '72010003',
                'address' => 'Teaoraereke, South Tarawa',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Naning Uriko',
                'email' => 'naning.uriko@counsel.org',
                'phone' => '72010004',
                'address' => 'Eita, South Tarawa',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Iareti Boitie',
                'email' => 'iareti.boitie@lawfirm.ki',
                'phone' => '72010005',
                'address' => 'Ambo, South Tarawa',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
