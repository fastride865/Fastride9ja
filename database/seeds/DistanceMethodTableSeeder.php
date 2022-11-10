<?php

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class DistanceMethodTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default distance methods
         $distance_methods = array(
            array('id' => '1','method' => 'SnapToRoad and Way Point','method_status' => '1'),
            array('id' => '2','method' => 'SnapToRoad and Aerial','method_status' => '1'),
            array('id' => '3','method' => 'Only Aerial','method_status' => '1'),
            array('id' => '4','method' => 'Meter Distance','method_status' => '1'),
            array('id' => '5','method' => 'Google Distance Pick n Drop','method_status' => '1'),
            array('id' => '6','method' => 'Google Distance Start n End','method_status' => '1'),
            array('id' => '7','method' => 'Estimated Distance','method_status' => '1'),
            array('id' => '8','method' => 'Manual Driver Input','method_status' => '1'),
            array('id' => '9','method' => 'Long Ride Logic','method_status' => '1')
        );
        DB::table('distance_methods')->insert($distance_methods);
    }
}
