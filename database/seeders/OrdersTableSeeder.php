<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('orders')->insert([
            [
                'user_id' => 1,
                'order_date' => '2023-01-01',
            ],
            [
                'user_id' => 2,
                'order_date' => '2022-12-01',
            ],
            [
                'user_id' => 3,
                'order_date' => '2023-01-09',
            ],
            [
                'user_id' => 4,
                'order_date' => '2023-01-05',
            ],
            [
                'user_id' => 5,
                'order_date' => '2022-12-24',
            ],
            [
                'user_id' => 6,
                'order_date' => '2022-12-31',
            ],
        ]);
    }
}
