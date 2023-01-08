<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            [
                'name' => 'Product 1',
                'color' => 'red',
                'size' => 'small',
                'price' => 20000,
                'price_discount' => 5000,
            ],
            [
                'name' => 'Product 2',
                'color' => 'blue',
                'size' => 'medium',
                'price' => 20000,
                'price_discount' => 5000,
            ],
            [
                'name' => 'Product 3',
                'color' => 'green',
                'size' => 'large',
                'price' => 20000,
                'price_discount' => 5000,
            ],
            [
                'name' => 'Product 4',
                'color' => 'yellow',
                'size' => 'small',
                'price' => 20000,
                'price_discount' => 5000,
            ],
            [
                'name' => 'Product 5',
                'color' => 'orange',
                'size' => 'medium',
                'price' => 20000,
                'price_discount' => 5000,
            ],
        ]);
    }
}
