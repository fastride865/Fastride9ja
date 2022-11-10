<?php

use Illuminate\Database\Seeder;

class PaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // payment_method_type = 1 means amount will go to driver
        // payment_method_type = 2 means amount will go to merchant
        // payment_method_type = 3 means case will be handled later on
        /*
        For amole ewallet is on 6th position, for next changes we have to check code for ewallet
        */
        $arr_payment_methods = [
            ['payment_method' => 'Cash', 'payment_method_type' => 1, 'payment_method_status' => 1, 'payment_icon' => 'qhQT6SwoNJbrvqGcVZ0fGqwfk8MXvcqjuNTmw3P8.png','slug'=>"CASH"],
            ['payment_method' => 'Credit Card\ Debit Card', 'payment_method_type' => 2, 'payment_method_status' => 1, 'payment_icon' => 'GimZ5LMX8XQ7VEbn16xH4X8rHw3pZt9XP4CLJ7lq.png','slug'=>"CARD"],
            ['payment_method' => 'Wallet', 'payment_method_type' => 2, 'payment_method_status' => 1, 'payment_icon' => 'DnHascb4YjCZa5yMb5FbX4zAzGYn4hQibu61LVTj.png','slug'=>"WALLET"],
            ['payment_method' => 'Online Payment', 'payment_method_type' => 2, 'payment_method_status' => 1, 'payment_icon' => 'yjd4mqMYzgDbwJK58izkmXrMt1SDJ5SkyxXPMhFe.png','slug'=>"ONLINE"],
            ['payment_method' => 'Swipe card', 'payment_method_type' => 1, 'payment_method_status' => 1, 'payment_icon' => 'yjd4mqMYzgDbwJK58izkmXrMt1SDJ5SkyxXPMhFe.png','slug'=>"SWIPE_CARD"],
            ['payment_method' => 'Pay Later', 'payment_method_type' => 2, 'payment_method_status' => 1, 'payment_icon' => 'yjd4mqMYzgDbwJK58izkmXrMt1SDJ5SkyxXPMhFe.png','slug'=>"PAY_LATER"],
            ['payment_method' => 'EWallet', 'payment_method_type' => 3, 'payment_method_status' => 1, 'payment_icon' => 'yjd4mqMYzgDbwJK58izkmXrMt1SDJ5SkyxXPMhFe.png','slug'=>"E_WALLET"],
        ];
        foreach ($arr_payment_methods as $key => $value)
        {
            DB::table('payment_methods')->insert([
                'payment_method' => $value['payment_method'],
                'payment_method_type' => $value['payment_method_type'],
                'payment_method_status' => $value['payment_method_status'],
                'payment_icon' => $value['payment_icon'],
            ]);
        }
    }
}
