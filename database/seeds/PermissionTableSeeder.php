<?php

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default permission
//        $permissions = array(
//            array('id' => '1','parent_id' => '0','name' => 'dashboard','special_permission' => '0','display_name' => 'Dashboard','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:57','updated_at' => '2018-10-18 03:03:57'),
//            array('id' => '2','parent_id' => '0','name' => 'manualdispach','special_permission' => '0','display_name' => 'Manual Dispach','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//
//            // ride management
//            array('id' => '3','parent_id' => '0','name' => 'ride_management','special_permission' => '1','display_name' => 'Ride Management','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '4','parent_id' => '3','name' => 'active_ride','special_permission' => '0','display_name' => 'View Active Ride','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '5','parent_id' => '3','name' => 'completed_ride','special_permission' => '0','display_name' => 'View Completed Ride','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '6','parent_id' => '3','name' => 'canceled_ride','special_permission' => '0','display_name' => 'View Cancelled Ride','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '7','parent_id' => '3','name' => 'failed_ride','special_permission' => '0','display_name' => 'View Failed Ride','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '8','parent_id' => '3','name' => 'autocancel_ride','special_permission' => '0','display_name' => 'View Auto Cancel Ride','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-15 14:48:20'),
//            array('id' => '9','parent_id' => '3','name' => 'all_rides','special_permission' => '0','display_name' => 'View All Rides','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '216','parent_id' => '3','name' => 'ride_cancel_dispatch','special_permission' => '0','display_name' => 'Cancel Active Ride','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:05','updated_at' => '2018-10-18 03:04:05'),
//
//            // admin type or sub admin
//            array('id' => '10','parent_id' => '0','name' => 'sub_admin','special_permission' => '0','display_name' => 'Sub-Admin','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '11','parent_id' => '10','name' => 'view_admin','special_permission' => '0','display_name' => 'view','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '12','parent_id' => '10','name' => 'create_admin','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '13','parent_id' => '10','name' => 'edit_admin','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '14','parent_id' => '10','name' => 'delete_admin','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//
//            // admin roles
//            array('id' => '15','parent_id' => '0','name' => 'role','special_permission' => '0','display_name' => 'Role','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '16','parent_id' => '15','name' => 'view_role','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '17','parent_id' => '15','name' => 'create_role','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '18','parent_id' => '15','name' => 'edit_role','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '19','parent_id' => '15','name' => 'delete_role','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//
//            // corporate panel
//            array('id' => '20','parent_id' => '0','name' => 'corporate','special_permission' => '1','display_name' => 'Corporate','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '21','parent_id' => '20','name' => 'view_corporate','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '22','parent_id' => '20','name' => 'create_corporate','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:58','updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '23','parent_id' => '20','name' => 'edit_corporate','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '24','parent_id' => '20','name' => 'delete_corporate','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//
//            // user management
//            array('id' => '25','parent_id' => '0','name' => 'riders','special_permission' => '0','display_name' => 'Rider','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '26','parent_id' => '25','name' => 'view_rider','special_permission' => '0','display_name' => 'view','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '27','parent_id' => '25','name' => 'create_rider','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '28','parent_id' => '25','name' => 'edit_rider','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '29','parent_id' => '25','name' => 'delete_rider','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//
//            // view countries
//            array('id' => '30','parent_id' => '0','name' => 'countries','special_permission' => '0','display_name' => 'Countries','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '31','parent_id' => '30','name' => 'view_countries','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '32','parent_id' => '30','name' => 'create_countries','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '33','parent_id' => '30','name' => 'edit_countries','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '34','parent_id' => '30','name' => 'delete_countries','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//
//            // service area
//            array('id' => '35','parent_id' => '0','name' => 'service_area','special_permission' => '0','display_name' => 'Service Area','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '36','parent_id' => '35','name' => 'view_area','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '37','parent_id' => '35','name' => 'create_area','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '38','parent_id' => '35','name' => 'edit_area','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '39','parent_id' => '35','name' => 'delete_area','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//
//            // vehicle type
//            array('id' => '40','parent_id' => '0','name' => 'vehicle_type','special_permission' => '1','display_name' => 'Vehicle Type','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '41','parent_id' => '40','name' => 'view_vehicle_type','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '42','parent_id' => '40','name' => 'create_vehicle_type','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '43','parent_id' => '40','name' => 'edit_vehicle_type','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '44','parent_id' => '40','name' => 'delete_vehicle_type','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//
//            // vehicle make
//            array('id' => '45','parent_id' => '0','name' => 'vehicle_make','special_permission' => '1','display_name' => 'Vehicle Make','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '46','parent_id' => '45','name' => 'view_vehicle_make','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '47','parent_id' => '45','name' => 'create_vehicle_make','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '48','parent_id' => '45','name' => 'edit_vehicle_make','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '49','parent_id' => '45','name' => 'delete_vehicle_make','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//
//            // vehicle model
//            array('id' => '50','parent_id' => '0','name' => 'vehicle_model','special_permission' => '1','display_name' => 'Vehicle Model','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '51','parent_id' => '50','name' => 'view_vehicle_model','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '52','parent_id' => '50','name' => 'create_vehicle_model','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '53','parent_id' => '50','name' => 'edit_vehicle_model','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:00','updated_at' => '2018-10-18 03:04:00'),
//            array('id' => '54','parent_id' => '50','name' => 'delete_vehicle_model','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//
//            // Price Card for (Taxi+ Delivery+ Towing) segments
//            array('id' => '55','parent_id' => '0','name' => 'price_card','special_permission' => '1','display_name' => 'Taxi based Price Card','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '56','parent_id' => '55','name' => 'view_price_card','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '57','parent_id' => '55','name' => 'create_price_card','special_permission' => '0','display_name' => 'create','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '58','parent_id' => '55','name' => 'edit_price_card','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '59','parent_id' => '55','name' => 'delete_price_card','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//
//            // Package module
//            array('id' => '60','parent_id' => '0','name' => 'package','special_permission' => '1','display_name' => 'Packages','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '61','parent_id' => '60','name' => 'view_package','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '62','parent_id' => '60','name' => 'create_package','special_permission' => '0','display_name' => 'create','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '63','parent_id' => '60','name' => 'edit_package','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '64','parent_id' => '60','name' => 'delete_package','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//
//            array('id' => '65','parent_id' => '0','name' => 'view_transactions','special_permission' => '0','display_name' => 'Transactions','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//
//            // Referral module
//            array('id' => '66','parent_id' => '0','name' => 'refer','special_permission' => '0','display_name' => 'Refer','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '67','parent_id' => '66','name' => 'view_refer','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '68','parent_id' => '66','name' => 'edit_refer','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '69','parent_id' => '66','name' => 'create_refer','special_permission' => '0','display_name' => 'Create Refer','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '70','parent_id' => '66','name' => 'delete_refer','special_permission' => '0','display_name' => 'Delete Refer','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//
//            // sos module
//            array('id' => '71','parent_id' => '0','name' => 'sos_numbers','special_permission' => '0','display_name' => 'Sos Numbers','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:01','updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '72','parent_id' => '71','name' => 'view_sos_number','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '73','parent_id' => '71','name' => 'create_sos_number','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '74','parent_id' => '71','name' => 'edit_sos_number','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '75','parent_id' => '71','name' => 'delete_sos_number','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//
//            // SoS Request
//            array('id' => '76','parent_id' => '0','name' => 'sos_request','special_permission' => '0','display_name' => 'Sos Request','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '77','parent_id' => '76','name' => 'view_sos_request','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '78','parent_id' => '77','name' => 'delete_sos_request','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//
//            // promo code
//            array('id' => '79','parent_id' => '0','name' => 'promo_code','special_permission' => '0','display_name' => 'Promo Code','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '80','parent_id' => '79','name' => 'view_promo_code','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '81','parent_id' => '79','name' => 'create_promo_code','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '82','parent_id' => '79','name' => 'edit_promo_code','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '83','parent_id' => '79','name' => 'delete_promo_code','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//
//            // cancel reasons
//            array('id' => '84','parent_id' => '0','name' => 'cancel_reason','special_permission' => '0','display_name' => 'Cancel Reason','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '85','parent_id' => '84','name' => 'view_cancel_reason','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '86','parent_id' => '84','name' => 'create_cancel_reason','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '87','parent_id' => '84','name' => 'edit_cancel_reason','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:02','updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '89','parent_id' => '84','name' => 'delete_cancel_reason','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//
//            // Rating
//            array('id' => '90','parent_id' => '0','name' => 'ratings','special_permission' => '0','display_name' => 'Ratings','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//
//            // promotion module
//            array('id' => '91','parent_id' => '0','name' => 'promotion','special_permission' => '0','display_name' => 'Promotion','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//            array('id' => '92','parent_id' => '91','name' => 'view_promotion','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//            array('id' => '93','parent_id' => '91','name' => 'create_promotion','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//            array('id' => '94','parent_id' => '91','name' => 'edit_promotion','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//            array('id' => '95','parent_id' => '91','name' => 'delete_promotion','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//
//            // customer support
//            array('id' => '96','parent_id' => '0','name' => 'customer_support','special_permission' => '0','display_name' => 'Customer Support','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//
//            // maps module
//            array('id' => '97','parent_id' => '0','name' => 'maps','special_permission' => '0','display_name' => 'Maps','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//            array('id' => '98','parent_id' => '97','name' => 'view_driver_map','special_permission' => '0','display_name' => 'Driver Map','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//            array('id' => '99','parent_id' => '97','name' => 'view_heat_map','special_permission' => '0','display_name' => 'Heat Map','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//
//            // cms module
//            array('id' => '100','parent_id' => '0','name' => 'cms','special_permission' => '0','display_name' => 'Cms','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//            array('id' => '101','parent_id' => '100','name' => 'view_cms','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//            array('id' => '102','parent_id' => '100','name' => 'create_cms','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//            array('id' => '103','parent_id' => '100','name' => 'edit_cms','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//            array('id' => '104','parent_id' => '100','name' => 'delete_cms','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//
//            // document module
//            array('id' => '106','parent_id' => '0','name' => 'documents','special_permission' => '0','display_name' => 'Documents','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:03','updated_at' => '2018-10-18 03:04:03'),
//            array('id' => '107','parent_id' => '106','name' => 'view_documents','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:04','updated_at' => '2018-10-18 03:04:04'),
//            array('id' => '108','parent_id' => '106','name' => 'create_documents','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:04','updated_at' => '2018-10-18 03:04:04'),
//            array('id' => '109','parent_id' => '106','name' => 'edit_documents','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:04','updated_at' => '2018-10-18 03:04:04'),
//            array('id' => '110','parent_id' => '106','name' => 'delete_documents','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:04','updated_at' => '2018-10-18 03:04:04'),
//
//            // configuration module
//            array('id' => '111','parent_id' => '0','name' => 'configuration','special_permission' => '0','display_name' => 'Configuration','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:04','updated_at' => '2018-10-18 03:04:04'),
//            array('id' => '112','parent_id' => '111','name' => 'view_configuration','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:04','updated_at' => '2018-10-18 03:04:04'),
//            array('id' => '113','parent_id' => '111','name' => 'edit_configuration','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:04','updated_at' => '2018-10-18 03:04:04'),
//
//            // push notification configuration for onesignal or firebase
//            array('id' => '114','parent_id' => '0','name' => 'onesignal','special_permission' => '0','display_name' => 'Onesignal','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:04','updated_at' => '2018-10-18 03:04:04'),
//            array('id' => '115','parent_id' => '114','name' => 'view_onesignal','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:04','updated_at' => '2018-10-18 03:04:04'),
//            array('id' => '116','parent_id' => '114','name' => 'edit_onesignal','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:04:05','updated_at' => '2018-10-18 03:04:05'),
//
//            // 117 attached with ride
//
//
//            // taxi company modules
//            array('id' => '117','parent_id' => '0','name' => 'taxi_company','special_permission' => '1','display_name' => 'Taxi Company','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-02-25 16:17:33'),
//            array('id' => '118','parent_id' => '117','name' => 'create_taxi_company','special_permission' => '0','display_name' => 'Create Taxi Company','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-02-25 16:17:33'),
//            array('id' => '119','parent_id' => '117','name' => 'view_taxi_company','special_permission' => '0','display_name' => 'View Taxi Company','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '120','parent_id' => '117','name' => 'edit_taxi_company','special_permission' => '0','display_name' => 'Edit Taxi Company','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '121','parent_id' => '117','name' => 'delete_taxi_company','special_permission' => '0','display_name' => 'Delete Taxi Company','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//
//            // driver account details
//            array('id' => '122','parent_id' => '0','name' => 'driver_accounts','special_permission' => '0','display_name' => 'Driver Accounts','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-02-25 16:17:33'),
//            array('id' => '123','parent_id' => '122','name' => 'view_driver_accounts','special_permission' => '0','display_name' => 'View Driver Accounts','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-02-25 16:17:33'),
//
//            // report module
//            array('id' => '124','parent_id' => '0','name' => 'reports_charts','special_permission' => '0','display_name' => 'Reports & Charts','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-02-25 16:17:33'),
//            array('id' => '125','parent_id' => '124','name' => 'view_reports_charts','special_permission' => '0','display_name' => 'View Reports Charts','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-02-25 16:17:33'),
//
//            // franchisee module
//            array('id' => '126','parent_id' => '0','name' => 'franchisee','special_permission' => '1','display_name' => 'Franchisee','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-02-25 16:17:33'),
//            array('id' => '127','parent_id' => '126','name' => 'create_franchisee','special_permission' => '0','display_name' => 'Create Franchisee','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-02-25 16:17:33'),
//            array('id' => '128','parent_id' => '126','name' => 'view_franchisee','special_permission' => '0','display_name' => 'View Franchisee','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '129','parent_id' => '126','name' => 'edit_franchisee','special_permission' => '0','display_name' => 'Edit Franchisee','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '130','parent_id' => '126','name' => 'delete_franchisee','special_permission' => '0','display_name' => 'Delete Franchisee','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//
//            // hotel module
//            array('id' => '131','parent_id' => '0','name' => 'hotel','special_permission' => '1','display_name' => 'Hotel','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-02-25 16:17:33'),
//            array('id' => '132','parent_id' => '131','name' => 'view_hotel','special_permission' => '0','display_name' => 'View Hotel','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-02-25 16:17:33'),
//            array('id' => '133','parent_id' => '131','name' => 'create_hotel','special_permission' => '0','display_name' => 'Create Hotel','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '134','parent_id' => '131','name' => 'edit_hotel','special_permission' => '0','display_name' => 'Edit Hotel','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//            array('id' => '135','parent_id' => '131','name' => 'delete_hotel','special_permission' => '0','display_name' => 'Delete Hotel','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//
//
//            array('id' => '136','parent_id' => '0','name' => 'permission','special_permission' => '0','display_name' => 'Permission','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-02-25 16:17:33'),
//
//            // cashback module
//            array('id' => '137','parent_id' => '0','name' => 'cashback','special_permission' => '1','display_name' => 'Cashback','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-02-25 16:17:33'),
//            array('id' => '138','parent_id' => '137','name' => 'view_cashback','special_permission' => '0','display_name' => 'View Cashback','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-02-25 16:17:33'),
//            array('id' => '139','parent_id' => '137','name' => 'create_cashback','special_permission' => '0','display_name' => 'Create Cashback','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '140','parent_id' => '137','name' => 'edit_cashback','special_permission' => '0','display_name' => 'Edit Cashback','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '141','parent_id' => '137','name' => 'delete_cashback','special_permission' => '0','display_name' => 'Delete Cashback','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//
//            // email configuration
//            array('id' => '142','parent_id' => '0','name' => 'email_configuration','special_permission' => '0','display_name' => 'Email Configurations','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '143','parent_id' => '142','name' => 'view_email_configurations','special_permission' => '0','display_name' => 'View Email Configuration','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '144','parent_id' => '142','name' => 'edit_email_configurations','special_permission' => '0','display_name' => 'Edit Email Configuration','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//
//            // service types
//            array('id' => '145','parent_id' => '0','name' => 'service_types','special_permission' => '0','display_name' => 'Service Types','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '146','parent_id' => '145','name' => 'view_service_types','special_permission' => '0','display_name' => 'View Service Types','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '147','parent_id' => '145','name' => 'edit_service_types','special_permission' => '0','display_name' => 'Edit Service Types','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//
//            //navigation drawers
//            array('id' => '148','parent_id' => '0','name' => 'navigation_drawers','special_permission' => '0','display_name' => 'Navigation Drawers','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '149','parent_id' => '148','name' => 'view_navigation_drawers','special_permission' => '0','display_name' => 'View Navigation Drawers','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '150','parent_id' => '148','name' => 'edit_navigation_drawers','special_permission' => '0','display_name' => 'Edit Navigation Drawers','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//
//            // application url
//            array('id' => '151','parent_id' => '0','name' => 'applications_url','special_permission' => '0','display_name' => 'Applications Url','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '152','parent_id' => '151','name' => 'view_applications_url','special_permission' => '0','display_name' => 'View Applications URL','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '153','parent_id' => '151','name' => 'edit_applications_url','special_permission' => '0','display_name' => 'Edit Applications Url','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-03-13 05:08:14'),
//
//            // payment method update
//            array('id' => '154','parent_id' => '0','name' => 'payment_methods','special_permission' => '0','display_name' => 'Payment Methods','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '155','parent_id' => '154','name' => 'view_payment_methods','special_permission' => '0','display_name' => 'View Payment Methods','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-15 14:48:20'),
//            array('id' => '156','parent_id' => '154','name' => 'edit_payment_methods','special_permission' => '0','display_name' => 'Edit Payment Methods','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//
//            // language string module
//            array('id' => '157','parent_id' => '0','name' => 'language_strings','special_permission' => '0','display_name' => 'Language Strings','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '158','parent_id' => '157','name' => 'view_language_strings','special_permission' => '0','display_name' => 'View Language Strings','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-15 14:48:20'),
//            array('id' => '159','parent_id' => '157','name' => 'edit_language_strings','special_permission' => '0','display_name' => 'Edit Language Strings','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//
//            // security question
//            array('id' => '160','parent_id' => '0','name' => 'security_question','special_permission' => '1','display_name' => 'Security Question','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-15 14:48:20'),
//            array('id' => '161','parent_id' => '160','name' => 'create_security_question','special_permission' => '0','display_name' => 'Create Security Question','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-15 14:48:20'),
//            array('id' => '162','parent_id' => '160','name' => 'view_security_question','special_permission' => '0','display_name' => 'View Security Question','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '163','parent_id' => '160','name' => 'edit_security_question','special_permission' => '0','display_name' => 'Edit Security Question','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '164','parent_id' => '160','name' => 'delete_security_question','special_permission' => '0','display_name' => 'Delete Security Question','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//
//            // subscription package
//            array('id' => '165','parent_id' => '0','name' => 'subscription_package','special_permission' => '1','display_name' => 'Subscription Package','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-15 14:48:20'),
//            array('id' => '166','parent_id' => '165','name' => 'create_subscription_package','special_permission' => '0','display_name' => 'Create Subscription Package','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-15 14:48:20'),
//            array('id' => '167','parent_id' => '165','name' => 'view_subscription_package','special_permission' => '0','display_name' => 'View Subscription Package','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '168','parent_id' => '165','name' => 'edit_subscription_package','special_permission' => '0','display_name' => 'Edit Subscription Package','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '169','parent_id' => '165','name' => 'delete_subscription_package','special_permission' => '0','display_name' => 'Delete Subscription Package','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//
//            // pricing parameter
//            array('id' => '170','parent_id' => '0','name' => 'pricing_parameter','special_permission' => '1','display_name' => 'Pricing Parameter','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '171','parent_id' => '170','name' => 'create_pricing_parameter','special_permission' => '0','display_name' => 'Create Pricing Parameter','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-15 14:48:20'),
//            array('id' => '172','parent_id' => '170','name' => 'view_pricing_parameter','special_permission' => '0','display_name' => 'View Pricing Parameter','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '173','parent_id' => '170','name' => 'edit_pricing_parameter','special_permission' => '0','display_name' => 'Edit Pricing Parameter','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//
//
//            // driver modules
//            array('id' => '174','parent_id' => '0','name' => 'drivers','special_permission' => '0','display_name' => 'Driver','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '175','parent_id' => '174','name' => 'view_drivers','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '176','parent_id' => '174','name' => 'create_drivers','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '177','parent_id' => '174','name' => 'edit_drivers','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '178','parent_id' => '174','name' => 'delete_drivers','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '179','parent_id' => '174','name' => 'basic_driver_signup','special_permission' => '0','display_name' => 'Basic Driver Signup','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-15 14:48:20'),
//            array('id' => '180','parent_id' => '174','name' => 'pending_drivers_approval','special_permission' => '0','display_name' => 'Pending Drivers Approval','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '181','parent_id' => '174','name' => 'rejected_drivers','special_permission' => '0','display_name' => 'Reject Drivers','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '182','parent_id' => '174','name' => 'block_drivers','special_permission' => '0','display_name' => 'Block Drivers','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//            array('id' => '183','parent_id' => '174','name' => 'expired_driver_documents','special_permission' => '0','display_name' => 'Expired Driver Documents','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//
//
//           // surcharge
//            array('id' => '184','parent_id' => '0','name' => 'surcharge','special_permission' => '1','display_name' => 'SurCharge','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '185','parent_id' => '184','name' => 'view_surcharge','special_permission' => '0','display_name' => 'View SurCharge','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-15 14:48:20'),
//            array('id' => '186','parent_id' => '184','name' => 'edit_surcharge','special_permission' => '0','display_name' => 'Edit SurCharge','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//
//            // wallet recharge
//            array('id' => '187','parent_id' => '0','name' => 'wallet_recharge','special_permission' => '1','display_name' => 'Wallet Recharge','guard_name' => 'merchant','created_at' => '2019-02-23 11:40:26','updated_at' => '2019-03-27 05:08:14'),
//
//            // terms and conditions
//            array('id' => '188','parent_id' => '0','name' => 'child_terms_condition','special_permission' => '1','display_name' => 'Child Terms And Condition','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//            array('id' => '189','parent_id' => '188','name' => 'create_child_terms','special_permission' => '0','display_name' => 'Create Child Terms','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-06 09:45:39'),
//            array('id' => '190','parent_id' => '188','name' => 'view_child_terms','special_permission' => '1','display_name' => 'View Child Terms','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '191','parent_id' => '188','name' => 'edit_child_terms','special_permission' => '0','display_name' => 'Edit Child Terms','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//
//            array('id' => '192','parent_id' => '0','name' => 'driver_commission_choices','special_permission' => '1','display_name' => 'Driver Commission Choices','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-06 09:45:39'),
//            array('id' => '193','parent_id' => '192','name' => 'view_driver_commission_choices','special_permission' => '0','display_name' => 'View Driver Commission Choice','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-06 09:45:39'),
//            array('id' => '194','parent_id' => '192','name' => 'update_driver_commission_choices','special_permission' => '0','display_name' => 'Update Driver Commission Choice','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//
//            // bank account type
//            array('id' => '195','parent_id' => '0','name' => 'account-types','special_permission' => '1','display_name' => 'Bank Account Type','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-15 14:48:20'),
//            array('id' => '196','parent_id' => '195','name' => 'create-account-types','special_permission' => '0','display_name' => 'Create  Account Type','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-15 14:48:20'),
//            array('id' => '197','parent_id' => '195','name' => 'view-account-types','special_permission' => '0','display_name' => 'View Account Type','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//            array('id' => '198','parent_id' => '195','name' => 'edit-account-types','special_permission' => '0','display_name' => 'Edit Account Type','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '199','parent_id' => '195','name' => 'delete-account-types','special_permission' => '0','display_name' => 'Delete Account Type','guard_name' => 'merchant','created_at' => '2019-03-27 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//
//            // vehicle module
//            array('id' => '200','parent_id' => '0','name' => 'view_all_vehicles','special_permission' => '1','display_name' => 'View All Vehicles','guard_name' => 'merchant','created_at' => '2019-05-08 11:40:26','updated_at' => '2019-05-08 16:17:33'),
//            array('id' => '201','parent_id' => '200','name' => 'edit_vehicle','special_permission' => '0','display_name' => 'Edit Vehicle','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '202','parent_id' => '0','name' => 'pending_vehicle_apporvels','special_permission' => '0','display_name' => 'Pending Vehicle Approvals','guard_name' => 'merchant','created_at' => '2018-10-18 03:03:59','updated_at' => '2018-10-18 03:03:59'),
//            array('id' => '203','parent_id' => '202','name' => 'view_pending_vehicle_apporvels','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2019-03-06 05:08:14','updated_at' => '2019-03-27 05:08:14'),
//            array('id' => '204','parent_id' => '202','name' => 'delete_pending_vehicle_apporvels','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//
//
//            // website module
//            array('id' => '205','parent_id' => '0','name' => 'website_user_home','special_permission' => '1','display_name' => 'Website User Home','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '206','parent_id' => '0','name' => 'website_driver_home','special_permission' => '1','display_name' => 'Website Driver Home','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//
//
//            // handyman orders
//            array('id' => '207','parent_id' => '0','name' => 'handyman_booking','special_permission' => '1','display_name' => 'Handyman Bookings','guard_name' => 'merchant','created_at' => '2019-11-13 05:08:14','updated_at' => '2019-11-13 05:08:14'),
//
//            // Service Time Slots
//            array('id' => '208','parent_id' => '0','name' => 'service_time_slot','special_permission' => '1','display_name' => 'Service Time Slot','guard_name' => 'merchant','created_at' => '2019-11-13 05:08:14','updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '209','parent_id' => '208','name' => 'add_time_slot','special_permission' => '0','display_name' => 'Add Time Slot','guard_name' => 'merchant','created_at' => '2019-11-13 05:08:14','updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '210','parent_id' => '208','name' => 'view_time_slot','special_permission' => '0','display_name' => 'View Time Slot','guard_name' => 'merchant','created_at' => '2019-11-13 05:08:14','updated_at' => '2019-11-13 05:08:14'),
//
//            // Cashout Request
//            array('id' => '211','parent_id' => '0','name' => 'cash_out','special_permission' => '0','display_name' => 'CashOut Request','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '212','parent_id' => '211','name' => 'view_driver_cash_out','special_permission' => '0','display_name' => 'View Driver Request','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '213','parent_id' => '211','name' => 'edit_driver_cash_out','special_permission' => '0','display_name' => 'Edit Driver Request','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '214','parent_id' => '211','name' => 'view_business_segment_cash_out','special_permission' => '0','display_name' => 'View Business Segment Request','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '215','parent_id' => '211','name' => 'edit_business_segment_cash_out','special_permission' => '0','display_name' => 'Edit Business Segment Request','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//
//            // 216 already used with driver module
//
//            // Reward point
//            array('id' => '224','parent_id' => '0','name' => 'reward_points','special_permission' => '1','display_name' => 'Reward Points','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '225','parent_id' => '224','name' => 'view_reward','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '226','parent_id' => '224','name' => 'create_reward','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '227','parent_id' => '224','name' => 'edit_reward','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '228','parent_id' => '224','name' => 'delete_reward','special_permission' => '0','display_name' => 'Delete','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//
//            // weight unit module
//            array('id' => '238','parent_id' => '0','name' => 'weight_unit','special_permission' => '1','display_name' => 'Weight Unit','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '239','parent_id' => '238','name' => 'view_weight_unit','special_permission' => '0','display_name' => 'View','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '240','parent_id' => '238','name' => 'create_weight_unit','special_permission' => '0','display_name' => 'Create','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '241','parent_id' => '238','name' => 'edit_weight_unit','special_permission' => '0','display_name' => 'Edit','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
////            array('id' => '242','parent_id' => '0','name' => 'delivery_configuration','special_permission' => '1','display_name' => 'Delivery Configuration','guard_name' => 'merchant','created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//
//            // business segment(restro+stores+shops)
//            array('id' => '243','parent_id' => '0','name' => 'business_segment','special_permission' => '1','display_name' => 'Business Segment','guard_name' => 'merchant','created_at' => '2019-11-13 05:08:14','updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '244','parent_id' => '243','name' => 'create_business_segment','special_permission' => '0','display_name' => 'Create Business Segment','guard_name' => 'merchant','created_at' => '2019-11-13 05:08:14','updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '245','parent_id' => '243','name' => 'view_business_segment','special_permission' => '0','display_name' => 'View Business Segment','guard_name' => 'merchant','created_at' => '2019-11-13 05:08:14','updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '246','parent_id' => '243','name' => 'order_statistics','special_permission' => '0','display_name' => 'View Order statistics','guard_name' => 'merchant','created_at' => '2019-11-13 05:08:14','updated_at' => '2019-11-13 05:08:14'),
//
//
//            //category module
//            array('id' => '247','parent_id' => '0','name' => 'category','special_permission' => '1','display_name' => 'Category','guard_name' => 'merchant','created_at' => '2020-08-25 05:08:14','updated_at' => '2020-08-25 05:08:14'),
//            array('id' => '248','parent_id' => '247','name' => 'add_category','special_permission' => '0','display_name' => 'Add Category','guard_name' => 'merchant','created_at' => '2020-08-25 05:08:14','updated_at' => '2020-08-25 05:08:14'),
//            array('id' => '249','parent_id' => '247','name' => 'view_category','special_permission' => '0','display_name' => 'View Category','guard_name' => 'merchant','created_at' => '2020-08-25 05:08:14','updated_at' => '2020-08-25 05:08:14'),
//
//            //Banner module
//            array('id' => '250','parent_id' => '0','name' => 'advertisement_banner','special_permission' => '1','display_name' => 'Advertisement Banner','guard_name' => 'merchant','created_at' => '2020-08-25 05:08:14','updated_at' => '2020-08-25 05:08:14'),
//            array('id' => '251','parent_id' => '250','name' => 'add_banner','special_permission' => '0','display_name' => 'Add Banner','guard_name' => 'merchant','created_at' => '2020-08-25 05:08:14','updated_at' => '2020-08-25 05:08:14'),
//            array('id' => '252','parent_id' => '250','name' => 'view_banner','special_permission' => '0','display_name' => 'View Banner','guard_name' => 'merchant','created_at' => '2020-08-25 05:08:14','updated_at' => '2020-08-25 05:08:14'),
//
//
//            // driver price card
//            array('id' => '256','parent_id' => '0','name' => 'driver_price_card','special_permission' => '1','display_name' => 'Diver Price Card','guard_name' => 'merchant','created_at' => '2019-11-13 05:08:14','updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '257','parent_id' => '256','name' => 'add_driver_price_card','special_permission' => '0','display_name' => 'Add Diver Price Card','guard_name' => 'merchant','created_at' => '2019-11-13 05:08:14','updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '258','parent_id' => '256','name' => 'view_driver_price_card','special_permission' => '0','display_name' => 'View Diver Price Card','guard_name' => 'merchant','created_at' => '2019-11-13 05:08:14','updated_at' => '2019-11-13 05:08:14'),
//
//            // user price card
//            array('id' => '259','parent_id' => '0','name' => 'user_price_card','special_permission' => '1','display_name' => 'User Price Card','guard_name' => 'merchant','created_at' => '2019-11-13 05:08:14','updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '260','parent_id' => '259','name' => 'add_user_price_card','special_permission' => '0','display_name' => 'Add User Price Card','guard_name' => 'merchant','created_at' => '2019-11-13 05:08:14','updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '261','parent_id' => '259','name' => 'view_user_price_card','special_permission' => '0','display_name' => 'View User Price Card','guard_name' => 'merchant','created_at' => '2019-11-13 05:08:14','updated_at' => '2019-11-13 05:08:14'),
//
//           // slots are empty from 211 to 223
//
//        );
//        // insert permissions into table
//        DB::table('permissions')->insert($permissions);

        /*
         * New Permissions
         */

//        $only_taxi_permissions = array(
//            // Manual dispatch
//            array('name' => 'manualdispach', 'special_permission' => '0', 'display_name' => 'Manual Dispach', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => []),
//
//            // corporate panel
//            array('name' => 'corporate', 'special_permission' => '1', 'display_name' => 'Corporate', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
//                array('name' => 'view_corporate', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'create_corporate', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'edit_corporate', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_corporate', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
//            )),
//
//            // taxi company modules
//            array('name' => 'taxi_company', 'special_permission' => '1', 'display_name' => 'Taxi Company', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
//                array('name' => 'create_taxi_company', 'special_permission' => '0', 'display_name' => 'Create Taxi Company', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'view_taxi_company', 'special_permission' => '0', 'display_name' => 'View Taxi Company', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'edit_taxi_company', 'special_permission' => '0', 'display_name' => 'Edit Taxi Company', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_taxi_company', 'special_permission' => '0', 'display_name' => 'Delete Taxi Company', 'guard_name' => 'merchant', 'permission_type' => 1),
//            )),
//
//            // franchisee module
//            array('name' => 'franchisee', 'special_permission' => '1', 'display_name' => 'Franchisee', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
//                array('name' => 'create_franchisee', 'special_permission' => '0', 'display_name' => 'Create Franchisee', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'view_franchisee', 'special_permission' => '0', 'display_name' => 'View Franchisee', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'edit_franchisee', 'special_permission' => '0', 'display_name' => 'Edit Franchisee', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_franchisee', 'special_permission' => '0', 'display_name' => 'Delete Franchisee', 'guard_name' => 'merchant', 'permission_type' => 1),
//            )),
//
//            // hotel module
//            array('name' => 'hotel', 'special_permission' => '1', 'display_name' => 'Hotel', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
//                array('name' => 'view_hotel', 'special_permission' => '0', 'display_name' => 'View Hotel', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'create_hotel', 'special_permission' => '0', 'display_name' => 'Create Hotel', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'edit_hotel', 'special_permission' => '0', 'display_name' => 'Edit Hotel', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_hotel', 'special_permission' => '0', 'display_name' => 'Delete Hotel', 'guard_name' => 'merchant', 'permission_type' => 1),
//            )),
//
//            // security question
//            array('name' => 'security_question', 'special_permission' => '1', 'display_name' => 'Security Question', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
//                array('name' => 'create_security_question', 'special_permission' => '0', 'display_name' => 'Create Security Question', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'view_security_question', 'special_permission' => '0', 'display_name' => 'View Security Question', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'edit_security_question', 'special_permission' => '0', 'display_name' => 'Edit Security Question', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_security_question', 'special_permission' => '0', 'display_name' => 'Delete Security Question', 'guard_name' => 'merchant', 'permission_type' => 1),
//            )),
//
//            // subscription package
//            array('name' => 'subscription_package', 'special_permission' => '1', 'display_name' => 'Subscription Package', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
//                array('name' => 'create_subscription_package', 'special_permission' => '0', 'display_name' => 'Create Subscription Package', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'view_subscription_package', 'special_permission' => '0', 'display_name' => 'View Subscription Package', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'edit_subscription_package', 'special_permission' => '0', 'display_name' => 'Edit Subscription Package', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_subscription_package', 'special_permission' => '0', 'display_name' => 'Delete Subscription Package', 'guard_name' => 'merchant', 'permission_type' => 1),
//            )),
//
//            // Reward point
//            array('name' => 'reward_points', 'special_permission' => '1', 'display_name' => 'Reward Points', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
//                array('name' => 'view_reward', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'create_reward', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'edit_reward', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_reward', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
//            )),
//
//            // Package module
//            array('name' => 'package', 'special_permission' => '1', 'display_name' => 'Packages', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
//                array('name' => 'view_package', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'create_package', 'special_permission' => '0', 'display_name' => 'create', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'edit_package', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_package', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
//            )),
//
//            // Driver Commission Choice
//            array('name' => 'driver_commission_choices', 'special_permission' => '1', 'display_name' => 'Driver Commission Choices', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
//                array('name' => 'view_driver_commission_choices', 'special_permission' => '0', 'display_name' => 'View Driver Commission Choice', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'update_driver_commission_choices', 'special_permission' => '0', 'display_name' => 'Update Driver Commission Choice', 'guard_name' => 'merchant', 'permission_type' => 1),
//            )),
//
//            //navigation drawers
//            array('name' => 'navigation_drawers', 'special_permission' => '0', 'display_name' => 'Navigation Drawers', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
//                array('name' => 'view_navigation_drawers', 'special_permission' => '0', 'display_name' => 'View Navigation Drawers', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'edit_navigation_drawers', 'special_permission' => '0', 'display_name' => 'Edit Navigation Drawers', 'guard_name' => 'merchant', 'permission_type' => 1),
//            )),
//
//            // surcharge
//            array('name' => 'surcharge', 'special_permission' => '1', 'display_name' => 'SurCharge', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
//                array('name' => 'view_surcharge', 'special_permission' => '0', 'display_name' => 'View SurCharge', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'edit_surcharge', 'special_permission' => '0', 'display_name' => 'Edit SurCharge', 'guard_name' => 'merchant', 'permission_type' => 1),
//            )),
//        );

//        $food_based_permissions = array(
//            array('name' => 'business_segment', 'special_permission' => '1', 'display_name' => 'Business Segment', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
//                array('name' => 'create_business_segment', 'special_permission' => '0', 'display_name' => 'Create Business Segment', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'view_business_segment', 'special_permission' => '0', 'display_name' => 'View Business Segment', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'order_statistics', 'special_permission' => '0', 'display_name' => 'View Order statistics', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'ride_management', 'special_permission' => '1', 'display_name' => 'Ride Management', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'price_card', 'special_permission' => '1', 'display_name' => 'Taxi based Price Card', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'promo_code', 'special_permission' => '0', 'display_name' => 'Promo Code', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'cancel_reason', 'special_permission' => '0', 'display_name' => 'Cancel Reason', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'business_segment_cash_out', 'special_permission' => '0', 'display_name' => 'Business Segment Request', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'weight_unit', 'special_permission' => '1', 'display_name' => 'Weight Unit', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'category', 'special_permission' => '1', 'display_name' => 'Category', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'service_time_slot', 'special_permission' => '1', 'display_name' => 'Service Time Slot', 'guard_name' => 'merchant', 'permission_type' => 1),
//            )),
//        );
//
//        $grocery_Base_permissions = array(
//            array('name' => 'business_segment', 'special_permission' => '1', 'display_name' => 'Business Segment', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
//                array('name' => 'create_business_segment', 'special_permission' => '0', 'display_name' => 'Create Business Segment', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'view_business_segment', 'special_permission' => '0', 'display_name' => 'View Business Segment', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'order_statistics', 'special_permission' => '0', 'display_name' => 'View Order statistics', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'ride_management', 'special_permission' => '1', 'display_name' => 'Ride Management', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'price_card', 'special_permission' => '1', 'display_name' => 'Taxi based Price Card', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'promo_code', 'special_permission' => '0', 'display_name' => 'Promo Code', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'cancel_reason', 'special_permission' => '0', 'display_name' => 'Cancel Reason', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'business_segment_cash_out', 'special_permission' => '0', 'display_name' => 'Business Segment Request', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'weight_unit', 'special_permission' => '1', 'display_name' => 'Weight Unit', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'category', 'special_permission' => '1', 'display_name' => 'Category', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'service_time_slot', 'special_permission' => '1', 'display_name' => 'Service Time Slot', 'guard_name' => 'merchant', 'permission_type' => 1),
//            )),
//        );
//
//        $handyman_segment_permissions = array(
//            array('name' => 'ride_management', 'special_permission' => '1', 'display_name' => 'Ride Management', 'guard_name' => 'merchant', 'permission_type' => 1),
//            array('name' => 'price_card', 'special_permission' => '1', 'display_name' => 'Taxi based Price Card', 'guard_name' => 'merchant', 'permission_type' => 1),
//            array('name' => 'service_time_slot', 'special_permission' => '1', 'display_name' => 'Service Time Slot', 'guard_name' => 'merchant', 'permission_type' => 1),
//            array('name' => 'promo_code', 'special_permission' => '0', 'display_name' => 'Promo Code', 'guard_name' => 'merchant', 'permission_type' => 1),
//        );

        // Default permission
//        $permissions = array(
//
//            // ride management
//            array('id' => '3', 'parent_id' => '0', 'name' => 'ride_management', 'special_permission' => '1', 'display_name' => 'Ride Management', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:03:58', 'updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '4', 'parent_id' => '3', 'name' => 'active_ride', 'special_permission' => '0', 'display_name' => 'View Active Ride', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:03:58', 'updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '5', 'parent_id' => '3', 'name' => 'completed_ride', 'special_permission' => '0', 'display_name' => 'View Completed Ride', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:03:58', 'updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '6', 'parent_id' => '3', 'name' => 'canceled_ride', 'special_permission' => '0', 'display_name' => 'View Cancelled Ride', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:03:58', 'updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '7', 'parent_id' => '3', 'name' => 'failed_ride', 'special_permission' => '0', 'display_name' => 'View Failed Ride', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:03:58', 'updated_at' => '2018-10-18 03:03:58'),
//            array('id' => '8', 'parent_id' => '3', 'name' => 'autocancel_ride', 'special_permission' => '0', 'display_name' => 'View Auto Cancel Ride', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-03-06 05:08:14', 'updated_at' => '2019-03-15 14:48:20'),
//            array('id' => '9', 'parent_id' => '3', 'name' => 'all_rides', 'special_permission' => '0', 'display_name' => 'View All Rides', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-03-13 05:08:14', 'updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '216', 'parent_id' => '3', 'name' => 'ride_cancel_dispatch', 'special_permission' => '0', 'display_name' => 'Cancel Active Ride', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:05', 'updated_at' => '2018-10-18 03:04:05'),
//
//
//            // Price Card for (Taxi+ Delivery+ Towing) segments
//            array('id' => '55', 'parent_id' => '0', 'name' => 'price_card', 'special_permission' => '1', 'display_name' => 'Taxi based Price Card', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:01', 'updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '56', 'parent_id' => '55', 'name' => 'view_price_card', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:01', 'updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '57', 'parent_id' => '55', 'name' => 'create_price_card', 'special_permission' => '0', 'display_name' => 'create', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:01', 'updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '58', 'parent_id' => '55', 'name' => 'edit_price_card', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:01', 'updated_at' => '2018-10-18 03:04:01'),
//            array('id' => '59', 'parent_id' => '55', 'name' => 'delete_price_card', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:01', 'updated_at' => '2018-10-18 03:04:01'),
//
//
////            array('id' => '65', 'parent_id' => '0', 'name' => 'view_transactions', 'special_permission' => '0', 'display_name' => 'Transactions', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:01', 'updated_at' => '2018-10-18 03:04:01'),
//
//            // promo code
//            array('id' => '79', 'parent_id' => '0', 'name' => 'promo_code', 'special_permission' => '0', 'display_name' => 'Promo Code', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:02', 'updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '80', 'parent_id' => '79', 'name' => 'view_promo_code', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:02', 'updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '81', 'parent_id' => '79', 'name' => 'create_promo_code', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:02', 'updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '82', 'parent_id' => '79', 'name' => 'edit_promo_code', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:02', 'updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '83', 'parent_id' => '79', 'name' => 'delete_promo_code', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:02', 'updated_at' => '2018-10-18 03:04:02'),
//
//            // cancel reasons
//            array('id' => '84', 'parent_id' => '0', 'name' => 'cancel_reason', 'special_permission' => '0', 'display_name' => 'Cancel Reason', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:02', 'updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '85', 'parent_id' => '84', 'name' => 'view_cancel_reason', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:02', 'updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '86', 'parent_id' => '84', 'name' => 'create_cancel_reason', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:02', 'updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '87', 'parent_id' => '84', 'name' => 'edit_cancel_reason', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:02', 'updated_at' => '2018-10-18 03:04:02'),
//            array('id' => '89', 'parent_id' => '84', 'name' => 'delete_cancel_reason', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:03', 'updated_at' => '2018-10-18 03:04:03'),
//
//            // Rating
////            array('id' => '90', 'parent_id' => '0', 'name' => 'ratings', 'special_permission' => '0', 'display_name' => 'Ratings', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2018-10-18 03:04:03', 'updated_at' => '2018-10-18 03:04:03'),
//
//
//            // driver account details
////            array('id' => '122', 'parent_id' => '0', 'name' => 'driver_accounts', 'special_permission' => '0', 'display_name' => 'Driver Accounts', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-02-23 11:40:26', 'updated_at' => '2019-02-25 16:17:33'),
////            array('id' => '123', 'parent_id' => '122', 'name' => 'view_driver_accounts', 'special_permission' => '0', 'display_name' => 'View Driver Accounts', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-02-23 11:40:26', 'updated_at' => '2019-02-25 16:17:33'),
//
//
//            // cashback module
////            array('id' => '137', 'parent_id' => '0', 'name' => 'cashback', 'special_permission' => '1', 'display_name' => 'Cashback', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-02-23 11:40:26', 'updated_at' => '2019-02-25 16:17:33'),
////            array('id' => '138', 'parent_id' => '137', 'name' => 'view_cashback', 'special_permission' => '0', 'display_name' => 'View Cashback', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-02-23 11:40:26', 'updated_at' => '2019-02-25 16:17:33'),
////            array('id' => '139', 'parent_id' => '137', 'name' => 'create_cashback', 'special_permission' => '0', 'display_name' => 'Create Cashback', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-03-13 05:08:14', 'updated_at' => '2019-03-13 05:08:14'),
////            array('id' => '140', 'parent_id' => '137', 'name' => 'edit_cashback', 'special_permission' => '0', 'display_name' => 'Edit Cashback', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-03-13 05:08:14', 'updated_at' => '2019-03-13 05:08:14'),
////            array('id' => '141', 'parent_id' => '137', 'name' => 'delete_cashback', 'special_permission' => '0', 'display_name' => 'Delete Cashback', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-03-27 05:08:14', 'updated_at' => '2019-03-27 05:08:14'),
//
//
//            // handyman orders
//            array('id' => '207', 'parent_id' => '0', 'name' => 'handyman_booking', 'special_permission' => '1', 'display_name' => 'Handyman Bookings', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-11-13 05:08:14', 'updated_at' => '2019-11-13 05:08:14'),
//
//            // Service Time Slots
//            array('id' => '208', 'parent_id' => '0', 'name' => 'service_time_slot', 'special_permission' => '1', 'display_name' => 'Service Time Slot', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-11-13 05:08:14', 'updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '209', 'parent_id' => '208', 'name' => 'add_time_slot', 'special_permission' => '0', 'display_name' => 'Add Time Slot', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-11-13 05:08:14', 'updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '210', 'parent_id' => '208', 'name' => 'view_time_slot', 'special_permission' => '0', 'display_name' => 'View Time Slot', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-11-13 05:08:14', 'updated_at' => '2019-11-13 05:08:14'),
//
//            // 216 already used with driver module
//
//
//            // weight unit module
//            array('id' => '238', 'parent_id' => '0', 'name' => 'weight_unit', 'special_permission' => '1', 'display_name' => 'Weight Unit', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-03-13 05:08:14', 'updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '239', 'parent_id' => '238', 'name' => 'view_weight_unit', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-03-13 05:08:14', 'updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '240', 'parent_id' => '238', 'name' => 'create_weight_unit', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-03-13 05:08:14', 'updated_at' => '2019-03-13 05:08:14'),
//            array('id' => '241', 'parent_id' => '238', 'name' => 'edit_weight_unit', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-03-13 05:08:14', 'updated_at' => '2019-03-13 05:08:14'),
////            array('id' => '242','parent_id' => '0','name' => 'delivery_configuration','special_permission' => '1','display_name' => 'Delivery Configuration','guard_name' => 'merchant', 'permission_type' => 1, created_at' => '2019-03-13 05:08:14','updated_at' => '2019-03-13 05:08:14'),
//
//            // business segment(restro+stores+shops)
//            array('id' => '243', 'parent_id' => '0', 'name' => 'business_segment', 'special_permission' => '1', 'display_name' => 'Business Segment', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-11-13 05:08:14', 'updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '244', 'parent_id' => '243', 'name' => 'create_business_segment', 'special_permission' => '0', 'display_name' => 'Create Business Segment', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-11-13 05:08:14', 'updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '245', 'parent_id' => '243', 'name' => 'view_business_segment', 'special_permission' => '0', 'display_name' => 'View Business Segment', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-11-13 05:08:14', 'updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '246', 'parent_id' => '243', 'name' => 'order_statistics', 'special_permission' => '0', 'display_name' => 'View Order statistics', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-11-13 05:08:14', 'updated_at' => '2019-11-13 05:08:14'),
//
//
//            //category module
//            array('id' => '247', 'parent_id' => '0', 'name' => 'category', 'special_permission' => '1', 'display_name' => 'Category', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2020-08-25 05:08:14', 'updated_at' => '2020-08-25 05:08:14'),
//            array('id' => '248', 'parent_id' => '247', 'name' => 'add_category', 'special_permission' => '0', 'display_name' => 'Add Category', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2020-08-25 05:08:14', 'updated_at' => '2020-08-25 05:08:14'),
//            array('id' => '249', 'parent_id' => '247', 'name' => 'view_category', 'special_permission' => '0', 'display_name' => 'View Category', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2020-08-25 05:08:14', 'updated_at' => '2020-08-25 05:08:14'),
//
//
//            // driver price card
//            array('id' => '256', 'parent_id' => '0', 'name' => 'driver_price_card', 'special_permission' => '1', 'display_name' => 'Diver Price Card', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-11-13 05:08:14', 'updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '257', 'parent_id' => '256', 'name' => 'add_driver_price_card', 'special_permission' => '0', 'display_name' => 'Add Diver Price Card', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-11-13 05:08:14', 'updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '258', 'parent_id' => '256', 'name' => 'view_driver_price_card', 'special_permission' => '0', 'display_name' => 'View Diver Price Card', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-11-13 05:08:14', 'updated_at' => '2019-11-13 05:08:14'),
//
//            // user price card
//            array('id' => '259', 'parent_id' => '0', 'name' => 'user_price_card', 'special_permission' => '1', 'display_name' => 'User Price Card', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-11-13 05:08:14', 'updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '260', 'parent_id' => '259', 'name' => 'add_user_price_card', 'special_permission' => '0', 'display_name' => 'Add User Price Card', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-11-13 05:08:14', 'updated_at' => '2019-11-13 05:08:14'),
//            array('id' => '261', 'parent_id' => '259', 'name' => 'view_user_price_card', 'special_permission' => '0', 'display_name' => 'View User Price Card', 'guard_name' => 'merchant', 'permission_type' => 1, 'created_at' => '2019-11-13 05:08:14', 'updated_at' => '2019-11-13 05:08:14'),
//
//            // slots are empty from 211 to 223
//        );

        $new_permissions = array(
            array("name" => "dashboard", 'special_permission' => '0', "display_name" => "Dashboard", 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => []),
            // admin type or sub admin
            array('name' => 'sub_admin', 'special_permission' => '0', 'display_name' => 'Sub-Admin', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_admin', 'special_permission' => '0', 'display_name' => 'view', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'create_admin', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_admin', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'delete_admin', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1)
            )),
            // admin roles
            array('name' => 'role', 'special_permission' => '0', 'display_name' => 'Role', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_role', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'create_role', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_role', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'delete_role', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),
            // user management
            array('name' => 'riders', 'special_permission' => '0', 'display_name' => 'Rider', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_rider', 'special_permission' => '0', 'display_name' => 'view', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'create_rider', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_rider', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'delete_rider', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),
            // view countries
            array('name' => 'countries', 'special_permission' => '0', 'display_name' => 'Countries', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_countries', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'create_countries', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_countries', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_countries', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),
            // service area
            array('name' => 'service_area', 'special_permission' => '0', 'display_name' => 'Service Area', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_area', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'create_area', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_area', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_area', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // vehicle type
            array('name' => 'vehicle_type', 'special_permission' => '1', 'display_name' => 'Vehicle Type', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_vehicle_type', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'create_vehicle_type', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_vehicle_type', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_vehicle_type', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // vehicle make
            array('name' => 'vehicle_make', 'special_permission' => '1', 'display_name' => 'Vehicle Make', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_vehicle_make', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'create_vehicle_make', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_vehicle_make', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_vehicle_make', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // vehicle model
            array('name' => 'vehicle_model', 'special_permission' => '1', 'display_name' => 'Vehicle Model', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_vehicle_model', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'create_vehicle_model', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_vehicle_model', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_vehicle_model', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),
            // Referral module
            array('name' => 'refer', 'special_permission' => '0', 'display_name' => 'Refer', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_refer', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_refer', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'create_refer', 'special_permission' => '0', 'display_name' => 'Create Refer', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'delete_refer', 'special_permission' => '0', 'display_name' => 'Delete Refer', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // sos module
            array('name' => 'sos_numbers', 'special_permission' => '0', 'display_name' => 'Sos Numbers', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_sos_number', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'create_sos_number', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_sos_number', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'delete_sos_number', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // SoS Request
            array('name' => 'sos_request', 'special_permission' => '0', 'display_name' => 'Sos Request', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_sos_request', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_sos_request', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // promotion module
            array('name' => 'promotion', 'special_permission' => '0', 'display_name' => 'Promotion', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_promotion', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'create_promotion', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_promotion', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'delete_promotion', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // customer support
            array('name' => 'customer_support', 'special_permission' => '0', 'display_name' => 'Customer Support', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array()),

            // maps module
            array('name' => 'maps', 'special_permission' => '0', 'display_name' => 'Maps', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_driver_map', 'special_permission' => '0', 'display_name' => 'Driver Map', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'view_heat_map', 'special_permission' => '0', 'display_name' => 'Heat Map', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // cms module
            array('name' => 'cms', 'special_permission' => '0', 'display_name' => 'Cms', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_cms', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'create_cms', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_cms', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_cms', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // document module
            array('name' => 'documents', 'special_permission' => '0', 'display_name' => 'Documents', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_documents', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'create_documents', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_documents', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_documents', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // configuration module
            array('name' => 'configuration', 'special_permission' => '0', 'display_name' => 'Configuration', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_configuration', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_configuration', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // push notification configuration for onesignal or firebase
            array('name' => 'onesignal', 'special_permission' => '0', 'display_name' => 'Onesignal', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_onesignal', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_onesignal', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // report module
            array('name' => 'reports_charts', 'special_permission' => '0', 'display_name' => 'Reports & Charts', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_reports_charts', 'special_permission' => '0', 'display_name' => 'View Reports Charts', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            //Permission
//            array('name' => 'permission', 'special_permission' => '0', 'display_name' => 'Permission', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => []),

            // email configuration
            array('name' => 'email_configuration', 'special_permission' => '0', 'display_name' => 'Email Configurations', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_email_configurations', 'special_permission' => '0', 'display_name' => 'View Email Configuration', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_email_configurations', 'special_permission' => '0', 'display_name' => 'Edit Email Configuration', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // service types
            array('name' => 'service_types', 'special_permission' => '0', 'display_name' => 'Service Types', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_service_types', 'special_permission' => '0', 'display_name' => 'View Service Types', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_service_types', 'special_permission' => '0', 'display_name' => 'Edit Service Types', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_segment', 'special_permission' => '0', 'display_name' => 'Edit Segment', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // application url
            array('name' => 'applications_url', 'special_permission' => '0', 'display_name' => 'Applications Url', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_applications_url', 'special_permission' => '0', 'display_name' => 'View Applications URL', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_applications_url', 'special_permission' => '0', 'display_name' => 'Edit Applications Url', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // payment method update
            array('name' => 'payment_methods', 'special_permission' => '0', 'display_name' => 'Payment Methods', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_payment_methods', 'special_permission' => '0', 'display_name' => 'View Payment Methods', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_payment_methods', 'special_permission' => '0', 'display_name' => 'Edit Payment Methods', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // language string module
            array('name' => 'language_strings', 'special_permission' => '0', 'display_name' => 'Language Strings', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_language_strings', 'special_permission' => '0', 'display_name' => 'View Language Strings', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_language_strings', 'special_permission' => '0', 'display_name' => 'Edit Language Strings', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // pricing parameter
            array('name' => 'pricing_parameter', 'special_permission' => '1', 'display_name' => 'Pricing Parameter', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'create_pricing_parameter', 'special_permission' => '0', 'display_name' => 'Create Pricing Parameter', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'view_pricing_parameter', 'special_permission' => '0', 'display_name' => 'View Pricing Parameter', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_pricing_parameter', 'special_permission' => '0', 'display_name' => 'Edit Pricing Parameter', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // driver modules
            array('name' => 'drivers', 'special_permission' => '0', 'display_name' => 'Driver', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_drivers', 'special_permission' => '0', 'display_name' => 'View', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'create_drivers', 'special_permission' => '0', 'display_name' => 'Create', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_drivers', 'special_permission' => '0', 'display_name' => 'Edit', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'delete_drivers', 'special_permission' => '0', 'display_name' => 'Delete', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'basic_driver_signup', 'special_permission' => '0', 'display_name' => 'Basic Driver Signup', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'pending_drivers_approval', 'special_permission' => '0', 'display_name' => 'Pending Drivers Approval', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'rejected_drivers', 'special_permission' => '0', 'display_name' => 'Reject Drivers', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'block_drivers', 'special_permission' => '0', 'display_name' => 'Block Drivers', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'expired_driver_documents', 'special_permission' => '0', 'display_name' => 'Expired Driver Documents', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // vehicle module
            array('name' => 'driver_vehicle', 'special_permission' => '1', 'display_name' => 'Driver Vehicle', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_all_vehicles', 'special_permission' => '0', 'display_name' => 'View All Vehicle', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_vehicle', 'special_permission' => '0', 'display_name' => 'Edit Vehicle', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'delete_vehicle', 'special_permission' => '0', 'display_name' => 'Delete Vehicle', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'view_pending_vehicle_apporvels', 'special_permission' => '0', 'display_name' => 'Pending Vehicle Approval', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'view_rejected_vehicles', 'special_permission' => '0', 'display_name' => 'Rejected Vehicles', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // Cashout Request
            array('name' => 'cash_out', 'special_permission' => '0', 'display_name' => 'CashOut Request', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'view_driver_cash_out', 'special_permission' => '0', 'display_name' => 'View Driver Request', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_driver_cash_out', 'special_permission' => '0', 'display_name' => 'Edit Driver Request', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // wallet recharge
            array('name' => 'wallet_recharge', 'special_permission' => '1', 'display_name' => 'Wallet Recharge', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array()),

            // terms and conditions
            array('name' => 'child_terms_condition', 'special_permission' => '1', 'display_name' => 'Child Terms And Condition', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'create_child_terms', 'special_permission' => '0', 'display_name' => 'Create Child Terms', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'view_child_terms', 'special_permission' => '1', 'display_name' => 'View Child Terms', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit_child_terms', 'special_permission' => '0', 'display_name' => 'Edit Child Terms', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // bank account type
            array('name' => 'account-types', 'special_permission' => '1', 'display_name' => 'Bank Account Type', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'create-account-types', 'special_permission' => '0', 'display_name' => 'Create  Account Type', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'view-account-types', 'special_permission' => '0', 'display_name' => 'View Account Type', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'edit-account-types', 'special_permission' => '0', 'display_name' => 'Edit Account Type', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'delete-account-types', 'special_permission' => '0', 'display_name' => 'Delete Account Type', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            // website module
            array('name' => 'website_user_home', 'special_permission' => '1', 'display_name' => 'Website User Home', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => []),
            array('name' => 'website_driver_home', 'special_permission' => '1', 'display_name' => 'Website Driver Home', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => []),

            //Banner module
            array('name' => 'advertisement_banner', 'special_permission' => '1', 'display_name' => 'Advertisement Banner', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
                array('name' => 'add_banner', 'special_permission' => '0', 'display_name' => 'Add Banner', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'view_banner', 'special_permission' => '0', 'display_name' => 'View Banner', 'guard_name' => 'merchant', 'permission_type' => 1),
                array('name' => 'delete_banner', 'special_permission' => '0', 'display_name' => 'Delete Banner', 'guard_name' => 'merchant', 'permission_type' => 1),
            )),

            //Banner module
//            array('name' => 'driver_agency', 'special_permission' => '1', 'display_name' => 'Driver Agency', 'guard_name' => 'merchant', 'permission_type' => 1, 'children' => array(
//                array('name' => 'add_agency', 'special_permission' => '0', 'display_name' => 'Add Agency', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'view_agency', 'special_permission' => '0', 'display_name' => 'View Agency', 'guard_name' => 'merchant', 'permission_type' => 1),
//                array('name' => 'delete_agency', 'special_permission' => '0', 'display_name' => 'Delete Agency', 'guard_name' => 'merchant', 'permission_type' => 1),
//            )),
        );

        DB::beginTransaction();
        try {
            foreach ($new_permissions as $permission) {
                $new_permission = new Permission();
                $new_permission->parent_id = 0;
                $new_permission->name = $permission['name'];
                $new_permission->special_permission = $permission['special_permission'];
                $new_permission->display_name = $permission['display_name'];
                $new_permission->guard_name = $permission['guard_name'];
                $new_permission->permission_type = $permission['permission_type'];
                $new_permission->save();
                if (!empty($permission['children'])) {
                    foreach ($permission['children'] as $child_permission) {
                        $new_child_permission = new Permission();
                        $new_child_permission->parent_id = $new_permission->id;
                        $new_child_permission->name = $child_permission['name'];
                        $new_child_permission->special_permission = $child_permission['special_permission'];
                        $new_child_permission->display_name = $child_permission['display_name'];
                        $new_child_permission->guard_name = $child_permission['guard_name'];
                        $new_child_permission->permission_type = $child_permission['permission_type'];
                        $new_child_permission->save();
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            p($e->getMessage());
        }
        DB::commit();
    }
}
