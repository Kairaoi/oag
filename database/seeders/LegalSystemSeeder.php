<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OAG\Draft\Ministry;
use App\Models\OAG\Draft\Counsel;
use App\Models\OAG\Draft\Bill;
use App\Models\OAG\Draft\Regulation;
use App\Models\OAG\Draft\BillCounsel;
use App\Models\OAG\Draft\RegulationCounsel;
use App\Models\OAG\Draft\BillHistory;
use App\Models\OAG\Draft\RegulationHistory;
use Carbon\Carbon;

class LegalSystemSeeder extends Seeder
{
    public function run()
    {
        // Seed Ministries
        $ministries = [
            ['name' => 'Office of Te Beretitenti', 'code' => 'OB'],
            ['name' => 'Ministry of Justice', 'code' => 'MOJ'],
            ['name' => 'Ministry of Finance and Economic Development', 'code' => 'MFED'],
            ['name' => 'Ministry of Education', 'code' => 'MOE'],
            ['name' => 'Ministry of Health and Medical Services', 'code' => 'MHMS'],
            ['name' => 'Ministry of Environment, Lands and Agriculture Development', 'code' => 'MELAD'],
            ['name' => 'Ministry of Fisheries and Marine Resources Development', 'code' => 'MFMRD'],
            ['name' => 'Ministry of Information, Communication, Transport and Tourism Development', 'code' => 'MICTTD'],
            ['name' => 'Ministry of Internal Affairs', 'code' => 'MIA'],
            ['name' => 'Ministry of Women, Youth, Sport and Social Affairs', 'code' => 'MWYSSA'],
        ];

        foreach ($ministries as $ministry) {
            Ministry::create($ministry);
        }

        // Seed Counsels
        $counsels = [
            [
                'name' => 'Tetiro Semilota',
                'position' => 'AG',
                'is_active' => true,
                'max_assignments' => 5
            ],
            [
                'name' => 'Monoo Mweretaka',
                'position' => 'DLD',
                'is_active' => true,
                'max_assignments' => 5
            ],
            [
                'name' => 'Raweai Tekiau',
                'position' => 'Senior Counsel',
                'is_active' => true,
                'max_assignments' => 5
            ],
            [
                'name' => 'Katutereiti Tong',
                'position' => 'Senior Counsel',
                'is_active' => true,
                'max_assignments' => 5
            ],
            [
                'name' => 'Teburoro Tito',
                'position' => 'Junior Counsel',
                'is_active' => true,
                'max_assignments' => 3
            ],
        ];

        foreach ($counsels as $counsel) {
            Counsel::create($counsel);
        }

        // Seed Bills
        $bills = [
            [
                'name' => 'Education Amendment Bill 2024',
                'receipt_date' => Carbon::now(),
                'ministry_id' => Ministry::where('code', 'MOE')->first()->id,
                'status' => 'Draft',
                'priority' => 'Normal',
                'task' => 'Review and update education policies',
                'progress_status' => 'Not Started',
                'comments' => 'Initial draft under review',
                'target_completion_date' => Carbon::now()->addMonths(3),
                'version' => '1.0'
            ],
            [
                'name' => 'Health Services Bill 2024',
                'receipt_date' => Carbon::now(),
                'ministry_id' => Ministry::where('code', 'MHMS')->first()->id,
                'status' => 'First Reading',
                'priority' => 'Urgent',
                'task' => 'Healthcare system reform',
                'progress_status' => 'Ongoing',
                'comments' => 'Preparing for first reading',
                'target_completion_date' => Carbon::now()->addMonths(2),
                'version' => '1.0'
            ],
            [
                'name' => 'Marine Resources Protection Bill 2024',
                'receipt_date' => Carbon::now(),
                'ministry_id' => Ministry::where('code', 'MFMRD')->first()->id,
                'status' => 'Draft',
                'priority' => 'High Priority',
                'task' => 'Marine conservation measures',
                'progress_status' => 'Not Started',
                'comments' => 'Initial consultation phase',
                'target_completion_date' => Carbon::now()->addMonths(4),
                'version' => '1.0'
            ],
        ];

        foreach ($bills as $bill) {
            $createdBill = Bill::create($bill);

            // Create bill history
            BillHistory::create([
                'bill_id' => $createdBill->id,
                'action' => 'Created',
                'changed_by' => 'System',
                'details' => 'Initial bill creation',
            ]);

            // Assign counsels to bills
            BillCounsel::create([
                'bill_id' => $createdBill->id,
                'counsel_id' => Counsel::inRandomOrder()->first()->id,
                'assigned_date' => Carbon::now(),
                'due_date' => Carbon::now()->addMonths(2),
                'role' => 'Lead'
            ]);
        }

        // Seed Regulations
        $regulations = [
            [
                'name' => 'Environmental Protection Regulation 2024',
                'receipt_date' => Carbon::now(),
                'ministry_id' => Ministry::where('code', 'MELAD')->first()->id,
                'status' => 'Pending',
                'priority' => 'High Priority',
                'comments' => 'Environmental impact assessment required',
                'target_completion_date' => Carbon::now()->addMonths(1),
                'requires_cabinet_approval' => true,
                'version' => '1.0'
            ],
            [
                'name' => 'Financial Services Regulation 2024',
                'receipt_date' => Carbon::now(),
                'ministry_id' => Ministry::where('code', 'MFED')->first()->id,
                'status' => 'In Review',
                'priority' => 'Normal',
                'comments' => 'Stakeholder consultation ongoing',
                'target_completion_date' => Carbon::now()->addMonths(2),
                'requires_cabinet_approval' => true,
                'version' => '1.0'
            ],
            [
                'name' => 'Tourism Development Regulation 2024',
                'receipt_date' => Carbon::now(),
                'ministry_id' => Ministry::where('code', 'MICTTD')->first()->id,
                'status' => 'Pending',
                'priority' => 'Normal',
                'comments' => 'Initial draft preparation',
                'target_completion_date' => Carbon::now()->addMonths(3),
                'requires_cabinet_approval' => true,
                'version' => '1.0'
            ],
        ];

        foreach ($regulations as $regulation) {
            $createdRegulation = Regulation::create($regulation);

            // Create regulation history
            RegulationHistory::create([
                'regulation_id' => $createdRegulation->id,
                'action' => 'Created',
                'changed_by' => 'System',
                'details' => 'Initial regulation creation',
            ]);

            // Assign counsels to regulations
            RegulationCounsel::create([
                'regulation_id' => $createdRegulation->id,
                'counsel_id' => Counsel::inRandomOrder()->first()->id,
                'assigned_date' => Carbon::now(),
                'due_date' => Carbon::now()->addMonths(2),
                'role' => 'Lead'
            ]);
        }
    }
}