<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
//        $this->call(CustomersTableSeeder::class);
//        $this->call(ProductsTableSeeder::class);
//        $this->call(OrdersTableSeeder::class);
//        $this->call(OrderDetailsTableSeeder::class);
        $this->call(TransactionsTableSeeder::class);
    }
}
