<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class korbaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*DB::table('payment_options')->insert([
            'slug' => 'KORBA',
            'name' => 'Korba',
            'params' => '{"api_secret_key": "HMAC SECRET KEY","api_public_key": "HMAC CLIENT KEY","auth_token": "Client Id",callback_url": "Call back URL"}',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);*/
    }
}
