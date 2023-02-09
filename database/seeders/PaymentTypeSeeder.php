<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('payment_types')->insert([
            [
                'payment_type' => 'atm',
                'status' => 1,
            ],
            [
                'payment_type' => 'vnpay',
                'status' => 1,
            ],
            [
                'payment_type' => 'shipcod',
                'status' => 1,
            ],
        ]);
    }
}
