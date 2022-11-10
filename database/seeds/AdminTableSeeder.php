<?php

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default password for Apporio admin is - 1234567
        DB::table('admins')->insert([
            'name' => 'Apporio',
            'email' => 'hello@apporio.com',
            'password' => '$2y$10$08yPJYPFyL/KsQ15fEXX/eGerRk/pP51uGUgepOC1ARNszoJ9/sbW',
            'remember_token' => '',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
}
