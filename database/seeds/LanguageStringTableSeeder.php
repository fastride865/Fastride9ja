<?php

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class LanguageStringTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $language_strings = array(
            array('id' => '1','language_string' => 'Accept Booking'),
            array('id' => '2','language_string' => 'User Cancel'),
            array('id' => '3','language_string' => 'Ride Now Booking'),
            array('id' => '4','language_string' => 'Start To PickUp'),
            array('id' => '5','language_string' => 'Arrive'),
            array('id' => '6','language_string' => 'PickUp'),
            array('id' => '7','language_string' => 'Drop'),
            array('id' => '8','language_string' => 'Start Ride'),
            array('id' => '9','language_string' => 'End Ride'),
            array('id' => '10','language_string' => 'Driver Cancel'),
            array('id' => '11','language_string' => 'Dispatcher Cancel'),
            array('id' => '12','language_string' => 'Arrive at location'),
            array('id' => '13','language_string' => 'Start From Pickup Location'),
            array('id' => '14','language_string' => 'Arriving'),
            array('id' => '15','language_string' => 'Driver is waiting at Pickup'),
            array('id' => '16','language_string' => 'Ride Started'),
            array('id' => '17','language_string' => 'Ride Ended'),
            array('id' => '18','language_string' => 'Your document(s) are under review'),
            array('id' => '19','language_string' => 'Ride Now'),
            array('id' => '20','language_string' => 'Ride Later'),
            array('id' => '21','language_string' => 'Your Otp For Verification is'),
            array('id' => '23','language_string' => 'Fare to be confirmed by driver'),
            array('id' => '24','language_string' => 'Your account verification OTP is'),
            array('id' => '25','language_string' => 'New Booking'),
            array('id' => '26','language_string' => 'There is new up-coming booking'),
            array('id' => '27','language_string' => 'Your  ride has been assigned to driver'),
            array('id' => '28','language_string' => 'Driver Start To Pickup Location'),
            array('id' => '29','language_string' => 'Haven\'t tried %s User Application yet? Sign up with my code(%s) and enjoy the most affordable can rides! '),
            array('id' => '30','language_string' => 'Your Ride Assign To Driver'),
            array('id' => '31','language_string' => 'Arrived at pickup location'),
            array('id' => '32','language_string' => 'Driver ride started'),
            array('id' => '33','language_string' => 'Driver reached at pickup point'),
            array('id' => '34','language_string' => 'Driver completed ride'),
            array('id' => '35','language_string' => 'Your wallet balance is low. Please recharge it by %s or more to make further booking'),
            array('id' => '36','language_string' => 'You are not authorize to take ride. Please contact you corporate support'),
            array('id' => '37','language_string' => 'Driver reached at drop point'),
        );
        DB::table('language_strings')->insert($language_strings);
    }
}
