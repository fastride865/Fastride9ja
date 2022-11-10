<?php

namespace App\Listeners;

use App\Events\WebPushNotificationEvent;
use App\Models\Merchant;
use App\Models\MerchantWebOneSignal;
use App\Models\Onesignal;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WebPushNotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  WebPushNotificationEvent  $event
     * @return void
     */
     public function handle(WebPushNotificationEvent $event)
    {
        $merchant = Merchant::find($event->merchant_id);
        if($merchant->ActiveWebOneSignals->isNotEmpty()):

            $merchant->ActiveWebOneSignals->transform(function ($item, $key) {
                return $item->player_id;
            });
            $player_id = $merchant->ActiveWebOneSignals->toArray();
            $onesignal_redirect_url = route('merchant.activeride',['slug' => $event->booking->Segment->slag]);
//            switch ($event->type):
//                case 1:
                    $onesignal_redirect_url = route('merchant.activeride',['slug' => $event->booking->Segment->slag]);
//                    switch ($event->booking->service_type_id):
//                        case 1:
//                            $message = ($event->booking->booking_type == 1) ? trans('admin.new_normal_ride_now_booked') : trans('admin.new_normal_ride_later_booked');
//                            break;
//                        case 2:
//                            $message = ($event->booking->booking_type == 1) ? trans('admin.new_rental_ride_now_booked') : trans('admin.new_rental_ride_later_booked');
//                            break;
//                        case 3:
//                            $message = ($event->booking->booking_type == 1) ? trans('admin.new_transfer_ride_now_booked') : trans('admin.new_transfer_ride_later_booked');
//                            break;
//                        case 4:
//                            $message = ($event->booking->booking_type == 1) ? trans('admin.new_outstation_ride_now_booked') : trans('admin.new_outstation_ride_later_booked');
//                            break;
//                        case 5:
//                            $message = trans('admin.new_pool_ride_booked');
//                            break;
//                    endswitch;
//                    break;
//                case 2:
//                    $onesignal_redirect_url = route('merchant.activeride',['slug' => $event->booking->Segment->slag]);
//                    $message = trans('admin.please_assign_driver');
//                break;    
//            endswitch;

//            if ($event->booking->delivery_type_id) {
//                $message = ($event->booking->booking_type == 1) ? trans('admin.new_normal_ride_now_booked') : trans('admin.new_normal_ride_later_booked');
//            }
            $title = trans("$event->string_file.ride_request");
            $message = trans("$event->string_file.ride_request_message");
            //$message = ($event->booking->booking_type == 1) ? trans('admin.new_normal_ride_now_booked') : trans('admin.new_normal_ride_later_booked');
            Onesignal::MerchantWebPushMessage($player_id, $event->data, $message, $title, $event->merchant_id,$onesignal_redirect_url);
        endif;
    }
}
