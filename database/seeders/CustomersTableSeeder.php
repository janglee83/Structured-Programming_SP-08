<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('customers')->insert([
            [
                'user_id' => 1,
                'name' => 'John Smith',
                'email' => 'john@example.com',
                'city' => 'New York',
                'district' => 'Manhattan',
                'town' => 'Upper West Side',
                'address' => '123 Main Street',
                'phone' => '123-456-7890',
            ],
            [
                'user_id' => 2,
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'city' => 'Los Angeles',
                'district' => 'Downtown',
                'town' => 'Little Tokyo',
                'address' => '456 Oak Avenue',
                'phone' => '234-567-8901',
            ],
            [
                'user_id' => 3,
                'name' => 'SP 08',
                'email' => 'SP_08@example.com',
                'city' => 'Los Angeles',
                'district' => 'Downtown',
                'town' => 'Little Tokyo',
                'address' => '456 Oak Avenue',
                'phone' => '234-567-8901',
            ],
        ]);
    }
}
