<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $pages = array(
            array('slug' => 'terms_and_Conditions','page' => 'Terms and Conditions', 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('slug' => 'about_us','page' => 'About Us', 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('slug' => 'help_center','page' => 'Help Center', 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('slug' => 'privacy_policy','page' => 'Privacy Policy', 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('slug' => 'refund_policy','page' => 'Refund Policy', 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
        );
        DB::table('pages')->insert($pages);
    }
}
