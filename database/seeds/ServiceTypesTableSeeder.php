<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ServiceTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arr_service_type = [
            ['id'=>1,'segment_id'=>1,'serviceName' => 'Normal', 'serviceStatus' => 1, 'type' => 1],
            ['id'=>1,'segment_id'=>1,'serviceName' => 'Rental', 'serviceStatus' => 1, 'type' => 2],
            ['id'=>1,'segment_id'=>1,'serviceName' => 'Transfer', 'serviceStatus' => 1, 'type' => 3],
            ['id'=>1,'segment_id'=>1,'serviceName' => 'Outstation', 'serviceStatus' => 1, 'type' => 4],
            ['id'=>1,'segment_id'=>1,'serviceName' => 'Pool', 'serviceStatus' => 1, 'type' => 5],
            ['id'=>1,'segment_id'=>2,'serviceName' => 'Normal Delivery', 'serviceStatus' => 1, 'type' => 1],
            ['id'=>1,'segment_id'=>3,'serviceName' => 'Normal Food', 'serviceStatus' => 1, 'type' => 1],
            ['id'=>1,'segment_id'=>4,'serviceName' => 'Normal Grocery', 'serviceStatus' => 1, 'type' => 1],
            ['id'=>1,'segment_id'=>5,'serviceName' => 'Normal Towing', 'serviceStatus' => 1, 'type' => 1],
            ['id'=>1,'segment_id'=>6,'serviceName' => 'Normal Salon', 'serviceStatus' => 1, 'type' => 1],
            ];
        foreach ($arr_service_type as $key => $value)
        {
            DB::table('service_types')->insert([
                'segment_id'=>$value['segment_id'],
                'serviceName' => $value['serviceName'],
                'serviceStatus' => $value['serviceStatus'],
                'type' => $value['type'],
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);
        }
    }
}
