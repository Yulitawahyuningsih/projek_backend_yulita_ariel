<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'WELCOME10',
                'type' => 'percent',
                'value' => 10,
                'minimum_order' => 100000,
                'quota' => 100,
                'expired_at' => '2026-12-31',
            ],
            [
                'code' => 'DISKON50K',
                'type' => 'fixed',
                'value' => 50000,
                'minimum_order' => 200000,
                'quota' => 50,
                'expired_at' => '2026-12-31',
            ],
            [
                'code' => 'FASHION20',
                'type' => 'percent',
                'value' => 20,
                'minimum_order' => 150000,
                'quota' => 75,
                'expired_at' => '2026-12-31',
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }
    }
}