<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call([
             AdminTableSeeder::class,
             SegmentGroupsTableSeeder::class,
             SegmentsTableSeeder::class,
             ServiceTypesTableSeeder::class,
             PaymentMethodsTableSeeder::class,
             PaymentOptionsTableSeeder::class,
             RateCardTableSeeder::class,
             PermissionTableSeeder::class,
             LanguageTableSeeder::class,
             DistanceMethodTableSeeder::class,
             BillPeriodTableSeeder::class,
             korbaSeeder::class,
             AppNavigationDrawersTableSeeder::class,
             LanguageStringTableSeeder::class,
             PagesTableSeeder::class,
             SmsGatewaysTableSeeder::class,
             DriverCommissionChoiceTableSeeder::class,
         ]);
    }
}
