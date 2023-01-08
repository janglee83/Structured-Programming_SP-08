<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orders = DB::table('orders')->select('id')->get();
        $orderDetailsData = [];
        foreach ($orders as $order) {
            $orderDetailsData[] = [
                'order_id' => $order->id,
                'price' => 20000,
                'price_discount' => mt_rand(1, 15) * 1000,
                'quantity' => 1,
                'product_id' => 1,
            ];
            $orderDetailsData[] = [
                'order_id' => $order->id,
                'price' => 20000,
                'price_discount' => mt_rand(1, 19) * 1000,
                'quantity' => 2,
                'product_id' => 2,
            ];
        }
        DB::table('order_details')->insert($orderDetailsData);
    }
}
