<?php

use Illuminate\Database\Seeder;

class RateCardTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arr_payment_options = ['Variable','Fixed Price','Input Driver'];
        foreach ($arr_payment_options as $key => $value)
        {
            DB::table('rate_cards')->insert([
                'name' => $value,
            ]);
        }
    }
}
