<?php

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class BillPeriodTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bill_periods = array(
            array('id' => '1','name' => 'Daily'),
            array('id' => '2','name' => 'Weekly'),
            array('id' => '3','name' => 'Monthly'),
            array('id' => '4','name' => 'Instant')
        );
        DB::table('bill_periods')->insert($bill_periods);
    }
}
