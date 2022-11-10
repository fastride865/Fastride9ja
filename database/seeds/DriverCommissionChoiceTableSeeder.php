<?php

use App\Models\DriverCommissionChoice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DriverCommissionChoiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $commission_choice = array(
                array('id'=>1, 'slug'=>'Subscription Based', 'status'=>1, 'admin_delete'=>0, 'created_at'=>date('Y-m-d H:i:s'), 'updated_at'=>date('Y-m-d H:i:s'), 'deleted_at'=>NULL,
                ),
                array('id'=>2, 'slug'=>'Commission Based', 'status'=>1, 'admin_delete'=>0, 'created_at'=>date('Y-m-d H:i:s'), 'updated_at'=>date('Y-m-d H:i:s'), 'deleted_at'=>NULL,
                ),
        );
        DB::table('driver_commission_choices')->insert($commission_choice);
    }
}
