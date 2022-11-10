<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AppNavigationDrawersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $app_nav_drawers = array(
            array('name' => 'Trip History','image' => '1561542185_5d133e29ccff7_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Trip History'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Favourite Driver','image' => '1561542223_5d133e4f98cd0_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Favourite Driver'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Price Card','image' => '1561541940_5d133d3404eb1_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Price Card'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Wallet Activity','image' => '1561542322_5d133eb22eff6_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Wallet Activity'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Emergency Contacts','image' => '1561542367_5d133edf17914_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Emergency Contacts'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Child List','image' => '1561542414_5d133f0e048d6_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Child List'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Card List','image' => '1561542463_5d133f3f2f738_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Card List'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Terms And Condition','image' => '1561542494_5d133f5e61be9_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Terms And Condition'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Refer And Earn','image' => '1561542519_5d133f7761551_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Refer And Earn'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Promotion Views','image' => '1561542551_5d133f970b45b_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Promotion Views'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Logout','image' => '1561542598_5d133fc6b6d79_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Logout'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Contact Us','image' => '1561542682_5d13401a82160_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Contact Us'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'About Us','image' => '1561542749_5d13405d3c55e_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('About Us'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Privacy Policy','image' => '1561542770_5d13407263be2_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Privacy Policy'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Language','image' => '1561542800_5d1340907c01c_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Language'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Customer Support','image' => '1561542825_5d1340a983fc0_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Customer Support'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Reward Points','image' => '1562139320_5d1c5ab83df58_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Reward Points'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Refund Policy','image' => '1562139320_5d1c5ab83df58_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Refund Policy'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
            array('name' => 'Live Chat','image' => '1562139320_5d1c5ab83df58_drawer_icon.png', 'type' => '1', 'status' => '1', 'slug' => str_slug('Live Chat'), 'created_at' => Carbon::now()->toDateTimeString(), 'updated_at' => Carbon::now()->toDateTimeString()),
        );
        DB::table('app_navigation_drawers')->insert($app_nav_drawers);
    }
}
