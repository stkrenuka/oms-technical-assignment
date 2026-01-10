<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('order_statuses')->insert([
            ['name' => 'Draft', 'slug' => 'draft', 'sort_order' => 1],
            ['name' => 'Confirmed', 'slug' => 'confirmed', 'sort_order' => 2],
            ['name' => 'Processing', 'slug' => 'processing', 'sort_order' => 3],
            ['name' => 'Dispatched', 'slug' => 'dispatched', 'sort_order' => 4],
            ['name' => 'Delivered', 'slug' => 'delivered', 'sort_order' => 5],
            ['name' => 'Cancelled', 'slug' => 'cancelled', 'sort_order' => 6],
            ['name' => 'Refunded', 'slug' => 'refunded', 'sort_order' => 7],

        ]);
    }
}
