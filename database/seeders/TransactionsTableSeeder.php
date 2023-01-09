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
        $transactions =  [
            [
                "id" => 1,
              "customer_id" => 1,
              "order_id" => 1,
              "method" => "atm",
              "payment_code" => "SP120230108",
              "money" => 10000,
              "type" => "pay",
              "status" => "successful",
              "bank_code" => "vietcombank",
              "payment_date" => null,
              "created_at" => "2023-01-08T13:55:19.000000Z",
              "updated_at" => "2023-01-08T13:55:19.000000Z"
            ],
            [
                "id" => 2,
              "customer_id" => 2,
              "order_id" => 2,
              "method" => "atm",
              "payment_code" => "SP220230108",
              "money" => 10000,
              "type" => "pay",
              "status" => "failed",
              "bank_code" => "vietcombank",
              "payment_date" => null,
              "created_at" => "2023-01-08T13:55:19.000000Z",
              "updated_at" => "2023-01-08T13:55:19.000000Z"
            ],
            [
                "id" => 3,
              "customer_id" => 3,
              "order_id" => 3,
              "method" => "shipcod",
              "payment_code" => "SP320230108",
              "money" => 10000,
              "type" => "pay",
              "status" => "pending",
              "bank_code" => "",
              "payment_date" => null,
              "created_at" => "2023-01-08T13:55:19.000000Z",
              "updated_at" => "2023-01-08T13:55:19.000000Z"
            ],
            [
                "id" => 4,
              "customer_id" => 2,
              "order_id" => 4,
              "method" => "vnpay",
              "payment_code" => "SP420230105",
              "money" => 58000,
              "type" => "pay",
              "status" => "successful",
              "bank_code" => "viettinbank",
              "payment_date" => null,
              "created_at" => "2023-01-05T13:55:19.000000Z",
              "updated_at" => "2023-01-05T13:55:19.000000Z"
            ],
            [
                "id" => 5,
              "customer_id" => 3,
              "order_id" => 5,
              "method" => "vnpay",
              "payment_code" => "SP520230105",
              "money" => 18000,
              "type" => "refund",
              "status" => "successful",
              "bank_code" => "viettinbank",
              "payment_date" => null,
              "created_at" => "2023-01-05T13:55:19.000000Z",
              "updated_at" => "2023-01-05T13:55:19.000000Z"
            ],
            [
                "id" => 6,
              "customer_id" => 3,
              "order_id" => 6,
              "method" => "shipcod",
              "payment_code" => "SP320230106",
              "money" => 105000,
              "type" => "pay",
              "status" => "successful",
              "bank_code" => "",
              "payment_date" => null,
              "created_at" => "2023-01-06T13:55:19.000000Z",
              "updated_at" => "2023-01-06T13:55:19.000000Z"
            ]
        ];
        foreach ($transactions as $transaction) {
            Transaction::create($transaction);
        }
    }
}
