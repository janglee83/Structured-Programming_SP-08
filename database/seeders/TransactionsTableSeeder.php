<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $methods = ['atm', 'vnpay', 'shipcode'];
        $status = ['successful', 'failed', 'pending'];
        for ($i = 1; $i <= 3; $i++) {
            $s = $status[mt_rand(0, 2)];
            Transaction::create([
                'order_id' => $i,
                'customer_id' => $i,
                'method' => $methods[mt_rand(0, 2)],
                'payment_code' => Transaction::generateCode($i, now()),
                'money' => 10000,
                'status' => $s,
                'payment_date' => $s === 'successfil' ? now() : null,
                'type' => 'pay',
                'bank_code' => 'vietcombank'
            ]);
        }
    }
}
