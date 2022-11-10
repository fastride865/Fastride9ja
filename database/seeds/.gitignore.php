<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SmsGatewaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sms_gateways')->insert([
            'params' =>  '{\"api_key\":\"Username\",\"sender\":\"Sender\",\"auth_token\":\"Password\"}',
            'name' => 'WIREPICK',
            'description' =>'WIREPICK',
            'status' =>1,
            'environment' => 1,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
}
