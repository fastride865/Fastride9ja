<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SegmentGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arr_segments = [
            ['group_name' => 'Vehicle Based', 'status' => 1],
            ['group_name' => 'Helper Based', 'status' => 1],
            ['group_name' => 'Other Based', 'status' => 1],
        ];
        foreach ($arr_segments as $key => $value)
        {
            DB::table('segment_groups')->insert([
                'status' => $value['status'],
                'group_name' => $value['group_name'],
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);
        }
    }
}
