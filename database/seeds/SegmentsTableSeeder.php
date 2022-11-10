<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SegmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arr_segments = [
            ['icon' => 'holder_taxi.png', 'name' => 'Taxi', 'description' => 'Taxi', 'slag' => 'TAXI','segment_group_id' => 1,'sub_group_for_app' => 3,'sub_group_for_admin' => 1],
            ['icon' => 'holder_taxi.png', 'name' => 'Delivery', 'description' => 'Delivery', 'slag' => 'DELIVERY','segment_group_id' => 1,'sub_group_for_app' => 4,'sub_group_for_admin' => 1],
            ['icon' => 'holder_taxi.png', 'name' => 'Food', 'description' => 'Food', 'slag' => 'FOOD','segment_group_id' => 1,'sub_group_for_app' => 1,'sub_group_for_admin' => 2],
            ['icon' => 'holder_taxi.png', 'name' => 'Grocery', 'description' => 'Grocery', 'slag' => 'GROCERY','segment_group_id' => 1,'sub_group_for_app' => 2,'sub_group_for_admin' => 2],
            ['icon' => 'holder_taxi.png', 'name' => 'Towing', 'description' => 'Towing', 'slag' => 'TOWING','segment_group_id' => 2,'sub_group_for_app' => NULL,'sub_group_for_admin' => NULL],
            ['icon' => 'holder_taxi.png', 'name' => 'Salon', 'description' => 'Salon', 'slag' => 'SALON','segment_group_id' => 2,'sub_group_for_app' => NULL,'sub_group_for_admin' => NULL],
            ['icon' => 'holder_taxi.png', 'name' => 'Plumber', 'description' => 'Plumber', 'slag' => 'PLUMBER','segment_group_id' => 2,'sub_group_for_app' => NULL,'sub_group_for_admin' => NULL],
        ];
        foreach ($arr_segments as $key => $value)
        {
            DB::table('segments')->insert([
                'icon' => $value['icon'],
                'name' => $value['name'],
                'segment_group_id' => $value['segment_group_id'],
                'description' => $value['description'],
                'slag' => $value['slag'],
                'sub_group_for_app' => $value['sub_group_for_app'],
                'sub_group_for_admin' => $value['sub_group_for_admin'],
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);
        }
    }
}
