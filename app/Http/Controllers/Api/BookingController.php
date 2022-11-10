<?php

namespace App\Http\Controllers\Api;

use App\Events\SendUserInvoiceMailEvent;

//use App\Http\Controllers\Helper\Merchant;
use App\Http\Controllers\Helper\DistanceCalculation;
use App\Http\Controllers\Helper\PolygenController;
use App\Http\Controllers\Helper\DistanceController;
use App\Http\Controllers\Helper\ReferralController;
use App\Http\Controllers\Helper\RewardPoint;
use App\Http\Controllers\Helper\ExtraCharges;
use App\Http\Controllers\Helper\Toll;
use App\Http\Controllers\Helper\TwilioMaskingHelper;
use App\Http\Controllers\Merchant\WhatsappController;
use App\Http\Resources\DeliveryCheckoutResource;
use App\Models\BookingTransaction;
use App\Models\DriverSubscriptionRecord;
use App\Models\PaymentOption;
use App\Http\Controllers\PaymentMethods\RandomPaymentController;
use App\Models\PaymentOptionsConfiguration;
use App\Models\UserCard;
use App\Traits\BookingTrait;
use DateTime;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PaymentMethods\CancelPayment;
use App\Http\Controllers\PaymentMethods\Payment;
use App\Http\Controllers\Services\NormalController;
use App\Http\Controllers\Services\OutstationController;
use App\Http\Controllers\Services\PoolController;
use App\Http\Controllers\Services\RentalController;
use App\Http\Controllers\Services\TransferController;
use App\Models\ApplicationConfiguration;
use App\Models\Booking;
use App\Models\BookingCheckout;
use App\Models\BookingCheckoutPackage;
use App\Models\BookingConfiguration;
use App\Models\BookingCoordinate;
use App\Models\BookingDetail;
use App\Models\BookingRating;
use App\Models\BookingRequestDriver;
use App\Models\Configuration;
use App\Models\CountryArea;
use App\Models\Driver;
use App\Models\DriverCancelBooking;
use App\Models\DriverVehicle;
use App\Models\FavouriteDriver;
use App\Models\Merchant;
use App\Models\Onesignal;
use App\Models\PoolRideList;
use App\Models\PriceCard;
use App\Models\PromoCode;
use App\Models\QuestionUser;
use App\Models\Sos;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\HolderController;
use App\Http\Controllers\Helper\PriceController;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\FindDriverController;
use App\Http\Controllers\Helper\SmsController;
use App\Models\SmsConfiguration;
use App\Http\Controllers\Api\CashbackController;
use App\Traits\ImageTrait;
use App\Models\Outstanding;
use App\Http\Controllers\Helper\DriverRecords;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\BookingDeliveryDetails;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use App\Models\ServiceType;
use App\Http\Controllers\Helper\CommonController;
use App\Traits\PolylineTrait;
use App\Http\Controllers\PaymentMethods\PayPhone\PayPhoneController;

class BookingController extends Controller
{
    use ImageTrait, BookingTrait, ApiResponseTrait, MerchantTrait, PolylineTrait;


    public function checkBookingStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);

        }
        $booking = Booking::find($request->booking_id);
        $booking_status = $booking->booking_status;
        $data = array('booking_id' => $booking->id, 'booking_status' => (string)$booking_status);
        $string_file = $this->getStringFile(NULL, $booking->Merchant);
        return $this->successResponse(trans("$string_file.booking_status"), $data);
    }

    public function changePaymentDuringRide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->wherein('booking_status', [1002, 1003, 1004]);
                }),
            ],
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $booking_obj = new Booking;
            $booking = $booking_obj->getBookingBasicData($request);
            $old_payment_method = $booking->PaymentMethod->payment_method;
            $booking->payment_method_id = $request->payment_method_id;
            $booking->save();
            setLocal($booking->Driver->language);
            $string_file = $this->getStringFile(NULL, $booking->Merchant);
            $data = ['booking_id' => $booking->booking_id, 'booking_status' => $booking->booking_status];
            $title = trans($string_file.payment_method_changed);
            $message = trans("$string_file.payment_method_changed", ['old_method' => $old_payment_method,
            'new_method' => $booking->PaymentMethod->payment_method]);
            $notification_data['notification_type'] = "PAYMENT_CHANGE";
            $notification_data['segment_type'] = $booking->Segment->slag;
            $notification_data['segment_data'] = $data;
            $large_icon = $this->getNotificationLargeIconForBooking($booking);
            $arr_param = ['driver_id' => $booking->driver_id, 'data' => $data, 'message' => $message, 'merchant_id' => $booking->merchant_id, 'title' => $title, 'large_icon' => $large_icon];
            Onesignal::DriverPushMessage($arr_param);
            setLocal();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();

        $mes = trans("$string_file.payment_method_changed_successfully");
        return $this->successResponse($mes, []);
    }


    public function BookingStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => [
                'required',
                Rule::exists('bookings', 'id'),
            ],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $booking = Booking::select('id', 'booking_status')->find($request->booking_id);
        $message = CommonController::BookingStatus($booking->booking_status);
        return $this->successResponse($message, $booking);
    }

    public function BookingOtpVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id'),
            ],
            'otp' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $booking = Booking::select('id', 'ride_otp', 'ride_otp_verify', 'merchant_id')->find($request->booking_id);
        $string_file = $this->getStringFile(NULL, $booking->Merchant);
        if ($booking->ride_otp == $request->otp) {
            $booking->ride_otp_verify = 3;
            $booking->save();
            return $this->successResponse(trans("$string_file.otp_verified"), []);
        }

        return $this->failedResponse(trans("$string_file.invalid_otp_try_again"));
    }


    public function MakePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'card_id' => 'required_if:payment_method_id,2',
            'success' => 'required|integer',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $booking_obj = new Booking;
            $booking = $booking_obj->getBookingBasicData($request);
            $previous_payment_method = $booking->payment_method_id;
            $string_file = $this->getStringFile(NULL, $booking->Merchant);
            $is_payment_success = false;
            $status = false;
            if ($request->success == 1) { // PAYMENT DONE
                $is_payment_success = true;
                if (isset($request->outstanding_id) && $request->outstanding_id != '') {
                    Outstanding::where(['id' => $request->outstanding_id, 'user_id' => $booking->user_id, 'reason' => '2', 'pay_status' => 0])->update(['pay_status' => 1]);
                    $currency = $booking->CountryArea->Country->isoCode;
                    $payment = new Payment();
                    $booking_transaction_submit = $booking->BookingTransaction;
                    $arr_req_param = [
                        'merchant_id'=>$booking->merchant_id,
                        'booking_id'=>$booking->id,
                        'payment_method_id'=>$request->payment_method_id,
                        'amount'=>$booking_transaction_submit->customer_paid_amount,
                        'card_id'=>$request->card_id,
                        'user_id'=>$booking->user_id,
                        'currency'=>$currency,
                        'driver_sc_account_id'=>$booking->Driver->sc_account_id,
                        'booking_transaction'=>$booking_transaction_submit,
                        'request_from'=>'USER_MAKE_PAYMENT',
                    ];
                    $status = $payment->MakePayment($arr_req_param);
                }
                elseif ($request->payment_method_id == 6) {
                    $currency = $booking->CountryArea->Country->isoCode;
                    $payment = new Payment();
                    $booking_transaction_submit = $booking->BookingTransaction;
                    //p($booking_transaction_submit);
                    $arr_req_param = [
                        'merchant_id'=>$booking->merchant_id,
                        'booking_id'=>$booking->id,
                        'payment_method_id'=>$request->payment_method_id,
                        'amount'=>$booking_transaction_submit->customer_paid_amount,
                        'card_id'=>$booking->card_id,
                        'user_id'=>$booking->user_id,
                        'currency'=>$currency,
                        'driver_sc_account_id'=>$booking->Driver->sc_account_id,
                        'booking_transaction'=>$booking_transaction_submit,
                    ];
                    $status = $payment->MakePayment($arr_req_param);


                    $booking->payment_status = 1; // refreshing the payment status
                    // give the commission to driver
                    $this->updateRideAmountInDriverWallet($booking, $booking_transaction_submit);

                }
                else
                {
                    if ($request->payment_method_id == 3) {
                        $amount = $booking->BookingTransaction->online_payment;
                        $wallet_balance = $booking->User->wallet_balance;
                        if ($wallet_balance < $amount) {
                            return $this->failedResponse(trans("$string_file.low_wallet_warning"));
                        }

                        $paramArray = array(
                            'user_id' => $booking->user_id,
                            'booking_id' => $booking->id,
                            'amount' => $amount,
                            'narration' => (!empty($booking->id)) ? 4 : 8,
                            'platform' => 2,
                            'payment_method' => 1,
                        );
                        WalletTransaction::UserWalletDebit($paramArray);
                    }else{
                        $merchant = new \App\Http\Controllers\Helper\Merchant();
                        if ($previous_payment_method != 1 && $request->payment_method_id == 1) {
                            $booking->BookingTransaction->cash_payment = $booking->BookingTransaction->online_payment;
                            $booking->BookingTransaction->online_payment = '0.0';
                            $booking->BookingTransaction->trip_outstanding_amount = $merchant->TripCalculation(($booking->BookingTransaction->driver_total_payout_amount + $booking->BookingTransaction->amount_deducted_from_driver_wallet - $booking->BookingTransaction->cash_payment), $booking->merchant_id);
                            $booking->BookingTransaction->save();
                        }
                    }

                    $booking->payment_status = 1;
                    $booking->payment_method_id = $request->payment_method_id;
                    $booking->save();

                    // update driver wallet because payment done from user screen after ride end,
                    $booking_transaction_submit = $booking->BookingTransaction;
                    $this->updateRideAmountInDriverWallet($booking, $booking_transaction_submit, $booking->id);
                }
                // send mail of payment
                event(new SendUserInvoiceMailEvent($booking));
            } elseif ($request->success == 2) { //PAYMENT FAILED
                $booking->payment_method_id = $request->payment_method_id;
                $booking->save();
                $is_payment_success = false;
                BookingDetail::where([['booking_id', '=', $request->booking_id]])->update(['payment_failure' => 2]);
            } else // PAYMENT PENDING AND NEED TO SEND REQUEST
            {
                // send request to payphone server if payphone is enabled
                // check user number is payment option is payphone
                if (!empty($request->payment_option_id) && $request->payment_method_id == 4) {
                    $option = PaymentOption::select('slug', 'id')->where('id', $request->payment_option_id)->first();
                    if (!empty($option) && $option->slug == 'PAYPHONE') {
                        $booking_transaction = $booking->BookingTransaction;
                        $tax1 = $booking_transaction->tax_amount;
                        $tax2 = $booking_transaction->tax_amount;
                        $amount = $booking_transaction->customer_paid_amount + $tax1 + $tax2;
                        $arr_payment_details = [];
                        $arr_payment_details['amount'] = [
                            'amount' => $amount,
                            'tax' => $booking_transaction->tax_amount,
                            'amount_with_tax' => $booking_transaction->tax_amount,
                            'amount_without_tax' => $booking_transaction->customer_paid_amount - ($tax1 + $tax2),
                        ];
                        $arr_payment_details['booking_id'] = $booking->id;
                        $payment_option_config = $option->PaymentOptionConfiguration;
                        $payphone = new PayPhoneController;
                        $payphone_response = $payphone->paymentRequest($request, $payment_option_config, $arr_payment_details);
                        DB::commit();
                        return $this->successResponse(trans("$string_file.success"), $payphone_response);
                    }
                }
            }

            // commit the change of db
            DB::commit();

            // IF PAYMENT DONE THEN SEND NOTIFICATION
            // Notification will not be sent in case of outstanding payment
            if ($status || $is_payment_success) {
                if (empty($request->outstanding_id) && !empty($booking->driver_id)) {
                    $title = trans("$string_file.payment_success");
                    $message = trans("$string_file.payment_done");
                    $data['notification_type'] = 'ONLINE_PAYMENT_RECEIVED';
                    $data['segment_type'] = $booking->Segment->slag;
                    $data['segment_data'] = ['id'=>$booking->id,'handyman_order_id'=>NULL];
                    $arr_param = ['driver_id' => $booking->driver_id, 'data' => $data, 'message' => $message, 'merchant_id' => $booking->merchant_id, 'title' => $title, 'large_icon' => ""];
                    Onesignal::DriverPushMessage($arr_param);
                }
                return $this->successResponse(trans("$string_file.payment_done"));
            }
            return $this->failedResponse(trans("$string_file.payment_failed"));

        } catch (Exception $e) {
            DB::rollBack();
            return $this->failedResponse($e->getMessage());
        }
    }

    public function ApplyPromoCode(Request $request)
    {

        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL, $user->Merchant);
        $validator = [
            'checkout_id' => 'required|exists:booking_checkouts,id',
            'promo_code' => 'required|exists:promo_codes,promoCode',
        ];
        $promo_code_error_msg = trans("$string_file.invalid_promo_code");
        $message = [
            'promo_code.exists' => $promo_code_error_msg,
        ];
        $validator = Validator::make($request->all(), $validator, $message);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $checkout = BookingCheckout::find($request->checkout_id);
            $user_id = $checkout->user_id;
            $price_card_id = $checkout->price_card_id;
            $country_area_id = $checkout->country_area_id;
            $promocode = PromoCode::where([['segment_id', '=', $checkout->segment_id], ['country_area_id', '=', $country_area_id], ['promoCode', '=', $request->promo_code], ['deleted', '=', NULL], ['promo_code_status', '=', 1]])->first();
            if (empty($promocode)) {
                return $this->failedResponse($promo_code_error_msg);
            }
            $validity = $promocode->promo_code_validity;
            $start_date = $promocode->start_date;
            $end_date = $promocode->end_date;
            $currentDate = date("Y-m-d");
            if ($validity == 2 && ($currentDate < $start_date || $currentDate > $end_date)) {
                return $this->failedResponse(trans("$string_file.promo_code_expired_message"));
            }
            $promo_code_limit = $promocode->promo_code_limit;
            $total_useage = Booking::where([['promo_code', '=', $promocode->id], ['booking_status', '!=', 1016]])->count();
            if ($total_useage >= $promo_code_limit) {
                return $this->failedResponse(trans("$string_file.promo_code_expired_message"));
            }
            $promo_code_limit_per_user = $promocode->promo_code_limit_per_user;
            $use_by_user = Booking::where([['promo_code', '=', $promocode->id], ['user_id', '=', $user_id], ['booking_status', '!=', 1016]])->count();
            if ($use_by_user >= $promo_code_limit_per_user) {
                return $this->failedResponse(trans("$string_file.user_limit_promo_code_expired"));
            }
            $applicable_for = $promocode->applicable_for;
            //$newUser = Booking::where([['user_id', '=', $user_id],['booking_status','=',1005]])->count();
            $newUser = User::find($user_id);

            if ($applicable_for == 2 && $newUser->created_at < $promocode->updated_at) {
                return $this->failedResponse(trans("$string_file.promo_code_for_new_user"));
            }

            $order_minimum_amount = $promocode->order_minimum_amount;
            if (!empty($checkout->estimate_bill) && $checkout->estimate_bill < $order_minimum_amount) {
                $message = trans_choice("$string_file.promo_code_order_value", 3, ['AMOUNT' => $order_minimum_amount]);
                return $this->failedResponse(trans($message));
            }
            $checkout->promo_code = $promocode->id;
            $checkout->save();

            $bookingDataObj = new BookingDataController();
            $promo_params = $bookingDataObj->feedPromoCodeValue($checkout);
            $checkout = $checkout->fresh();
            $checkout->discounted_amount = $promo_params['discounted_amount'];

            if ($checkout->segment_id == 2) {
                $result = new DeliveryCheckoutResource($checkout);
            } else {
                $bookingData = new BookingDataController();
                $result = $bookingData->CheckOut($checkout);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.promo_code_applied"), $result);
    }

    public function RemovePromoCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checkout_id' => 'required|exists:booking_checkouts,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $checkout = BookingCheckout::find($request->checkout_id);
            $string_file = $this->getStringFile($checkout->merchant_id);
            if ($checkout->PromoCode) {
                $bill_details = json_decode($checkout->bill_details, true);
                $billDetailsParameterType = array_pluck($bill_details, 'parameterType');
                $index = in_array('PROMO CODE', $billDetailsParameterType) ? array_search('PROMO CODE', $billDetailsParameterType) : '';
                if ($index) {
                    unset($bill_details[$index]);
                    $checkout->bill_details = json_encode($bill_details);
                }
                $checkout->promo_code = null;
                $checkout->save();
            }
            if ($checkout->segment_id == 2) {
                $result = new DeliveryCheckoutResource($checkout);
            } else {
                $bookingData = new BookingDataController();
                $result = $bookingData->CheckOut($checkout);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.promo_remove"), $result);
    }

    public function paymentOption(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checkout_id' => 'required_without:booking_id|integer|exists:booking_checkouts,id',
            'booking_id' => 'required_without:checkout_id|integer|exists:bookings,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $ride_amount = NULL;
            if ($request->booking_id) {
                $data = Booking::select('id', 'price_card_id', 'country_area_id', 'final_amount_paid')->with('PriceCard')->find($request->booking_id);
                $ride_amount = $data->final_amount_paid;
            } else {
                $data = BookingCheckout::select('id', 'price_card_id', 'country_area_id')->with('PriceCard')->find($request->checkout_id);
            }
            $paymentMethods = $data->CountryArea->PaymentMethod;
            $Payment_ids = array_pluck($paymentMethods, 'id');
            $wallet_option = in_array(3, $Payment_ids) ? true : false;
            $currency = $data->CountryArea->Country->isoCode;
            $creditOption = in_array(2, $Payment_ids) ? true : false;
            $bookingData = new BookingDataController();
            $options = $bookingData->PaymentOption($paymentMethods, $request->user('api')->id, $currency, $data->PriceCard->minimum_wallet_amount, $ride_amount);
            if (isset($request->booking_id) && $request->booking_id != '') {
                $key_pay_later = array_search(6, array_column($options, 'id'));
                if (!empty($key_pay_later) && $key_pay_later >= 0) {
                    unset($options[$key_pay_later]);
                }
                // outstanding will clear only by online payment
                $user = $request->user('api');
                $out_standing_amount = Outstanding::where(['booking_id' => $request->booking_id, 'user_id' => $user->id, 'reason' => 2, 'pay_status' => 0])->sum('amount');
                if(!empty($out_standing_amount) && $out_standing_amount > 0)
                {
                    $key_cash = array_search(1, array_column($options, 'id'));
                    if(!empty($key_cash) && $key_cash >= 0){
                         unset($options[$key_cash]);
                   }
                }
                $options = array_values($options);
            }
            return $this->successResponse("success", $options);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
//        return response()->json(['result' => "1", 'message' => trans('admin.message534'), 'wallet_button' => $wallat_option, 'credit_button' => $creditOption, 'data' => $options]);
    }

    public function UserAutoCancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => ['required',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->where('booking_status', 1001);
                }),
            ]
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        DB::beginTransaction();
        try {
            $booking = Booking::find($request->booking_id);
            $booking_status = $booking->booking_status;
            if ($booking_status == 1001) {
                $bookingData = new BookingDataController();
                $booking_status = 1016;
                $booking->booking_status = $booking_status;
                if ($booking->promo_code != null) {
                    $promo_code = PromoCode::find($booking->promo_code);
                    if (!empty($promo_code)) {
                        $booking->promo_code = null;
                    }
                }
                $booking->save();
                // inset booking status history
                $this->saveBookingStatusHistory($request, $booking, $booking->id);
                $cancelDriver = BookingRequestDriver::with(['Driver' => function ($q) {
                    $q->addSelect('id', 'last_ride_request_timestamp', 'id as driver_id');
                }])->where([['booking_id', '=', $request->booking_id]])->get();
                $cancelDriver = array_pluck($cancelDriver, 'Driver');
                foreach ($cancelDriver as $key => $value) {
                    $value->last_ride_request_timestamp = date("Y-m-d H:i:s", time() - 100);
                    $value->save();
                }
                $bookingData->SendNotificationToDrivers($booking, $cancelDriver);
            }
        } catch (Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        DB::commit();
        $string_file = $this->getStringFile($booking->merchant_id);
        return $this->successResponse(trans("$string_file.ride_cancelled"), array('booking_id' => $booking->id, 'booking_status' => "$booking_status"));
    }

    public function completeBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->where('booking_status', 1005);
//                    $query->where('booking_closure', NULL);
                }),
            ],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new Exception($errors[0]);
        }
        DB::beginTransaction();
        try {
            $booking_id = $request->id;
            $booking = Booking::with('BookingDetail', 'PriceCard')->find($booking_id);
            $string_file = $this->getStringFile(NULL, $booking->Merchant);
            if ($booking->PriceCard->pricing_type == 3 && $booking->booking_closure != 1) {
                $refresh_screen = true;
                $validator = Validator::make($request->all(), [
                    'input_values' => 'required',
                ]);
                if ($validator->fails()) {
                    $errors = $validator->messages()->all();
                    throw new Exception($errors[0]);
                }
                $bookingDetails = BookingDetail::where([['booking_id', '=', $booking_id]])->first();
                $newArray = json_decode($request->input_values, true);
                if (sizeof(array_filter($newArray)) == 0) {
//                    throw new Exception(trans('api.amount_zero'));
                }
                $amount = array_sum(array_pluck($newArray, 'amount'));
                $maximum_bill_amount = $booking->PriceCard->maximum_bill_amount;
                if ($amount > $maximum_bill_amount || $amount == 0) {
//                    throw new Exception(trans('api.message64'));
                }
                $subTotalWithoutDiscount = $amount;
                if (!empty($booking->PromoCode)) {
                    $bookingDetails->total_amount = $amount;
                    $type = $booking->PromoCode->promo_code_value_type;
                    if ($type == 1) {
                        $promoDiscount = $booking->PromoCode->promo_code_value;
                    } else {
                        $promoDiscount = ($amount * $booking->PromoCode->promo_code_value) / 100;
                    }
                    if ($promoDiscount < $amount) {
                        $amount = $amount - $booking->PromoCode->promo_code_value;
                    } else {
                        $amount = "0.00";
                    }
                    $bookingDetails->promo_discount = $promoDiscount;
                } else {
                    $bookingDetails->total_amount = $amount;
                    $bookingDetails->promo_discount = "0.00";
                }
                $payment = new Payment();
                if ($amount > 0) {
                    $array_param = array(
                        'booking_id' => $booking->id,
                        'payment_method_id' => $booking->payment_method_id,
                        'amount' => $amount,
                        'quantity' => 1,
                        'order_name' => $booking->merchant_booking_id,
                        'user_id' => $booking->user_id,
                        'card_id' => $booking->card_id,
                        'currency' => $booking->CountryArea->Country->isoCode,
                    );
                    $payment->MakePayment($array_param);
//                    $payment->MakePayment($booking->id, $booking->payment_method_id, $amount, $booking->user_id, $booking->card_id);
                } else {
                    $payment->UpdateStatus(['booking_id' => $booking->id]);
                }
                $bookingDetails->bill_details = $request->input_values;
                $bookingDetails->save();

                if (!empty($taxes_array)):
                    $newArray = array_merge($newArray, $taxes_array);
                    $total_tax = array_sum(array_pluck($taxes_array, 'amount'));
                    $total_tax = sprintf('%0.2f', $total_tax);
                    $amount += $total_tax;
                endif;

                $booking->final_amount_paid = $amount;
                $booking->booking_closure = 1;
                $booking->save();
                $merchant_id = $booking->merchant_id;
                $user_id = $booking->user_id;
                $bookingData = new BookingDataController();
                $message = "";//trans('api.notification_driver_input_fare');
                $data = $bookingData->BookingNotification($booking);
                Onesignal::UserPushMessage($user_id, $data, $message, 4, $merchant_id);
            } else {
                if ($booking->payment_status == 1) {
                    $booking->booking_closure = 1;
                    $booking->save();
                    $refresh_screen = false;
                } else {
                    throw new Exception(trans("$string_file.payment_pending"));
                }
            }
            $booking_data = new BookingDataController;
            $rating = BookingRating::updateOrCreate(
                ['booking_id' => $booking_id],
                [
                    'driver_rating_points' => $request->rating,
                    'driver_comment' => $request->comment
                ]
            );
            $user_id = $booking->user_id;
            $avg = BookingRating::whereHas('Booking', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })->avg('driver_rating_points');
            $user = $booking->User;
            $user->rating = round($avg, 2);
            $user->save();
            $request->request->add(['booking_id' => $booking->id]);
            $return_data = $booking_data->bookingReceiptForDriver($request);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        DB::commit();
        return ['message' => trans("$string_file.ride_completed"), 'data' => $return_data];
    }

    // when driver changed drop location
    public function DriverChangeAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->where([['total_drop_location', '<=', 1]]);
                }),
            ],
            'location' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
//                response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }

        $booking = Booking::find($request->booking_id);
        $area = $booking->CountryArea;
//            ::find($booking->country_area_id);
        $merchant = $booking->Merchant;
//            ::find($booking->merchant_id);

        return $this->AddDropAddress($request->booking_id, $request->latitude, $request->longitude, $request->location, $area, 2);
//        if (!empty($area->DemoConfiguration) || ($merchant->Configuration->drop_outside_area == 1 && in_array($booking->service_type_id, [1, 2, 3, 5]))) {
//            return $this->AddDropAddress($request->booking_id, $request->latitude, $request->longitude, $request->location, $area, 2);
//        } else {
//            return $this->AddDropAddress($request->booking_id, $request->latitude, $request->longitude, $request->location, $area, 2);
//        }
    }

    // when user changed drop location
    public function UserChangeAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->where([['total_drop_location', '<=', 1]]);
                }),
            ],
            'location' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $booking = Booking::find($request->booking_id);
        $area = $booking->CountryArea;
//            CountryArea::find($booking->country_area_id);
        $merchant = $booking->Merchant;
//            Merchant::find($booking->merchant_id);
        return $this->AddDropAddress($request->booking_id, $request->latitude, $request->longitude, $request->location, $area, 1);

    }

    public function AddDropAddress($booking_id, $latitude, $longitude, $location, $area, $type)
    {
        $booking = Booking::find($booking_id);
        $string_file = $this->getStringFile($booking->merchant_id);
        $ploygon = new PolygenController();
        $checkArea = $ploygon->CheckArea($latitude, $longitude, $area->AreaCoordinates);


        if ($checkArea) {
            $booking->drop_latitude = $latitude;
            $booking->drop_longitude = $longitude;
            $booking->drop_location = $location;
            $booking->save();
            // update booking details table
            $details = $booking->BookingDetail;
            $details->end_latitude = $latitude;
            $details->end_longitude = $longitude;
            $details->end_location = $location;
            $details->save();

            if (!empty($booking->segment_id) && $booking->Segment->slug == "DELIVERY") {
                // if booking is from delivery then update delivery table.
                $delivery_details = $booking->BookingDeliveryDetails;
                $delivery_details->drop_latitude = $latitude;
                $delivery_details->drop_longitude = $longitude;
                $delivery_details->drop_location = $location;
                $delivery_details->save();
            }
            $merchant_id = $booking->merchant_id;
            $bookingDatata = new BookingDataController();

            $data['notification_type'] = 'DROP_CHANGED';
            $data['segment_type'] = $booking->Segment->slag;
            $data['segment_sub_group'] = $booking->Segment->sub_group_for_app; // its segment sub group for app
            $data['segment_group_id'] = $booking->Segment->segment_group_id; // for handyman
            $data['segment_data'] = [
                'booking_id' => $booking->id,
                'segment_slug' => $booking->Segment->slag
            ];

            if ($type == 1) {
                setLocal($booking->Driver->language);
                $title = trans("$string_file.drop_location_changed");
                $message = trans("$string_file.user_changed_drop_location");
                $arr_param = ['driver_id' => $booking->driver_id, 'data' => $data, 'message' => $message, 'merchant_id' => $merchant_id, 'title' => $title, 'large_icon' => ""];
                Onesignal::DriverPushMessage($arr_param);
                setLocal();
            } else {
                setLocal($booking->User->language);
                $title = trans("$string_file.drop_location_changed");
                $message = trans("$string_file.driver_changed_drop_location");
                $arr_param['data'] = $data;
                $arr_param['user_id'] = $booking->user_id;
                $arr_param['message'] = $message;
                $arr_param['merchant_id'] = $merchant_id;
                $arr_param['title'] = $title; // notification title
                $arr_param['large_icon'] = "";
                Onesignal::UserPushMessage($arr_param);
                setLocal();
            }
            $booking = $bookingDatata->DriverBookingDetails($booking_id, $merchant_id);
            return $this->successResponse($message, $booking);
        } else {
            return $this->failedResponse(trans("$string_file.drop_location_out"));
        }
    }

    public function DriverRating(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->where('booking_status', 1005);
                }),
            ],
            'rating' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $rating = BookingRating::updateOrCreate(
            ['booking_id' => $request->booking_id],
            [
                'driver_rating_points' => $request->rating,
                'driver_comment' => $request->comment
            ]
        );
        $booking = Booking::where([['id', '=', $request->booking_id]])->first();
        $user_id = $booking->user_id;
        $avg = BookingRating::whereHas('Booking', function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })->avg('driver_rating_points');
        $user = User::find($user_id);
        $user->rating = round($avg, 2);
        $user->save();
        $string_file =$this->getStringFile(null,$user->Merchant);
        return response()->json(['result' => "1", 'message' => trans("$string_file.rating_thanks"), 'data' => $rating]);
    }

    // booking rating by user to driver
    public function bookingRating(Request $request)
    {
        DB::beginTransaction();
        try {
            $booking_id = $request->id;
            $booking = Booking::select('id', 'driver_id', 'booking_status', 'vehicle_type_id')->find($booking_id);
            if ($booking->booking_status != 1005) {
                // will change this error as required
                throw new Exception(trans("error"));
            }
            $rating = BookingRating::updateOrCreate(
                ['booking_id' => $booking_id],
                [
                    'user_rating_points' => $request->rating,
                    'user_comment' => $request->comment,
                    'driver_vehicle_rating_points' => $request->vehicle_rating,
                    'driver_vehicle_comment' => $request->vehicle_comment,
                ]
            );
            $driver_id = $booking->driver_id;
            $avg = BookingRating::whereHas('Booking', function ($q) use ($driver_id) {
                $q->where('driver_id', $driver_id);
            })->avg('user_rating_points');
            $driver = Driver::select('id', 'rating')->find($driver_id);
            $driver->rating = round($avg, 2);
            $driver->save();

            $vehicle_type_id = $booking->vehicle_type_id;
            $avg_vehicle_rating = BookingRating::whereHas('Booking', function ($q) use ($driver_id, $vehicle_type_id) {
                $q->where([['driver_id', $driver_id], ['vehicle_type_id', '=', $vehicle_type_id]]);
            })->avg('driver_vehicle_rating_points');
            $vehicle_type = $booking->VehicleType;
            $vehicle_type->rating = round($avg_vehicle_rating, 2);
            $vehicle_type->save();
        } catch (Exception $e) {
            DB::rollBack();
        }
        DB::commit();
        return ['booking_order_id' => $booking_id];
    }

    public function UserReceipt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->where('booking_status', 1005);
                }),
            ],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $pay_later_enable = false;
        $pay_later_text = false;
        $booking = Booking::with('BookingDetail', 'Driver', 'PriceCard')->find($request->booking_id);
        $string_file = $this->getStringFile(NULL, $booking->Merchant);

        $config = ApplicationConfiguration::where('merchant_id', $booking->merchant_id)->first();

        $show_status = false;
        $tip_status = false;
        if ($config->tip_status == 1) {
            $show_status = true;
            if (!empty($booking->BookingDetail)) {

                if ($booking->BookingDetail->tip_amount > 0) {
                    $show_status = false;
                }
            }
            $tip_status = true;
        }

        $configuration = $booking->Merchant->Configuration;
        $out_standing_amount = Outstanding::where(['booking_id' => $booking->id, 'user_id' => $booking->user_id, 'reason' => 2, 'pay_status' => 0])->sum('amount');
//        if (isset($config->user_outstanding_enable) && $config->user_outstanding_enable && $out_standing_amount > 0) {
        if (isset($configuration->user_outstanding_enable) && $configuration->user_outstanding_enable &&
            $booking->payment_status !=1) {
            $pay_later_enable = true;
            $pay_later_text = trans("$string_file.pay_later_note");
        }
        $currency = $booking->CountryArea->Country->isoCode;
        $booking_closure = $booking->booking_closure;
        $appConfig = ApplicationConfiguration::select('favourite_driver_module', 'vehicle_rating_enable')->where([['merchant_id', '=', $booking->merchant_id]])->first();
        $payment_options = [];
        if ($booking->PriceCard->pricing_type == 3 && $booking_closure != 1) {
            $rating_visibility = false;
            $fav_visibility = false;
            $bottom_button_color = 'f1f1f1';
            $text_color = '333333';
            $text = "";//trans('api.message150');
            $action = "NO";
        } else {
            $rating_visibility = $booking->payment_status == 1 ? true : false;
            $text = $booking->payment_status == 1 ? trans("$string_file.complete") : trans("$string_file.select_payment_method");
            $action = $booking->payment_status == 1 ? "FINISH" : "SELECT_PAYMENT";
            if ($booking->payment_status != 1) {
                if ($booking->payment_method_id == 4 && $booking->BookingDetail->payment_failure != 2) {
                    $text = trans("$string_file.make_payment");
                    $action = "MAKE_PAYMENT";
                } elseif ($booking->payment_method_id == 2 && $booking->BookingDetail->payment_failure != 2) {
                    $payment_option_slug = $booking->UserCard->PaymentOption->slug;
                    if ($payment_option_slug == 'PayGate') {
                        $text = trans("$string_file.make_payment");
                        $action = "MAKE_PAYMENT";
                    }
                } else {
                    // @Bhuvanesh
                    // Where condition for cash payment & wallet method only
                    $paymentMethods = $booking->CountryArea->PaymentMethod->whereIn('id', [1, 3]);
                    $bookingData = new BookingDataController();
                    $payment_option = $bookingData->PaymentOption($paymentMethods, $request->user('api')->id, $currency, $booking->CountryArea->minimum_wallet_amount, $booking->final_amount_paid);

                    foreach ($payment_option as $val) {
                        // cash and wallet can be select again if payment was was failed with the same method
//                        might  user has done recharged after payment failed
                        if (($val['id'] != $booking->payment_method_id) || in_array($val['id'], [1, 3])) {
                            $payment_options[] = $val;
                        }
                    }
                }
            }
            $fav_visibility = $appConfig->favourite_driver_module == 1 ? true : false;
            $bottom_button_color = '333333';
            $text_color = 'ffffff';
        }
        if (!empty($booking->BookingDetail->bill_details)) {
            // $price = json_decode($booking->BookingDetail->bill_details);
            $price = json_decode($booking->bill_details);
            $holder = HolderController::PriceDetailHolder($price, $booking->id);
        } else {
            $holder = [];
        }

        $user = $request->user('api');
        $already_added = false;
        $user_favourite_drivers = $user->FavouriteDriver;
        if ($user_favourite_drivers->isNotEmpty()):
            $user_favourite_drivers = array_pluck($user_favourite_drivers->toArray(), 'driver_id');
            $already_added = (in_array($booking->driver_id, $user_favourite_drivers)) ? true : false;
        endif;

        $holder_driver_favourite = array(
            'visibility' => $fav_visibility,
            'driver_data' => array(
                "driver_id" => $booking->driver_id,
                'already_added' => $already_added,
                "booking_id" => $request->booking_id,
                "text" => isset($booking->Driver->fullName) ? $booking->Driver->fullName : '',
                "image" => isset($booking->Driver->profile_image) ? $booking->Driver->profile_image : ''
            )
        );
        $holder_driver_vehicle_rating = array(
            'visibility' => $appConfig->vehicle_rating_enable == 1 ? $rating_visibility : false,
            'vehicle_data' => array(
                "booking_id" => $request->booking_id,
                "text" => $booking->DriverVehicle->VehicleType->VehicleTypeName . '( ' . $booking->DriverVehicle->vehicle_color . ' )',
                "image" => get_image($booking->VehicleType->vehicleTypeImage, 'vehicle', $booking->merchant_id, true, false),
            )
        );

        // multiple drop location arr
        $multi_destination = $booking->Merchant->BookingConfiguration->multi_destination == 1 ? true : false;
        $arr_mid_ride_location = [];
        if ($multi_destination) {
            $drop_location = $booking->waypoints;
            $multiple_location = json_decode($drop_location, true);
            if (!empty($multiple_location)) {
                foreach ($multiple_location as $location) {
                    // currently end location value doesn't exist and to get that we have to run google api
                    $arr_mid_ride_location [] = [
                        'address' => $location['drop_location'],
                    ];
                }
            }
        }
        $payment_amount = $booking->final_amount_paid;
        $payment_amount = $payment_amount == "" ? trans("$string_file.details") : $payment_amount;
        $service_text = ($booking->service_type_id) ? trans("$string_file.service_type") . ' : ' . $booking->ServiceType->serviceName :
            trans("$string_file.delivery_type") . $booking->deliveryType->name;
        $result = array(
            'holder_ride_info' => array(
                'circular_image' => get_image($booking->VehicleType->vehicleTypeImage, 'vehicle', $booking->merchant_id, true, false),
                'circular_image_visibility' => true,
                'circular_text_one' => $service_text,
                'circular_text' => $booking->VehicleType->VehicleTypeName,
                "circular_text_style" => "",
                "circular_text_color" => "333333",
                "circular_text_visibility" => true,
                "value_text" => $currency . " " . $payment_amount,
                "value_text_style" => "BOLD",
                "value_text_color" => "2ecc71",
                "value_text_visibility" => true,
                "left_text" => $booking->travel_distance,
                "left_text_style" => "BOLD",
                "left_text_color" => "333333",
                "left_text_visibility" => true,
                "right_text" => $booking->travel_time,
                "right_text_style" => "BOLD",
                "right_text_color" => "333333",
                "right_text_visibility" => true,
                "pick_locaion" => $booking->BookingDetail->start_location,
                "pick_location_visibility" => true,
                "drop_location" => $booking->BookingDetail->end_location,
                "drop_location_visibility" => true,
                'static_values' => $holder,
                'multiple_drop_location' => $arr_mid_ride_location,
                'card_id' => $booking->card_id,
            ),
            'holder_driver_rating' => array(
                'visibility' => $rating_visibility,
                'driver_data' => array(
                    "booking_id" => $request->booking_id,
                    "text" => $booking->Driver->first_name . " " . $booking->Driver->last_name,
                    "image" => $booking->Driver->profile_image
                )
            ),
            'holder_driver_favourite' => $holder_driver_favourite,
            'holder_bottom_button' => array(
                'bottom_button_color' => $bottom_button_color,
                'text_color' => $text_color,
                'text' => $text,
                'action' => $action,
                'payment_method_id' => $booking->payment_method_id,
                'payment_data' => $payment_options
            ),
            'holder_driver_vehicle_rating' => $holder_driver_vehicle_rating,
            'pay_later_enable' => $pay_later_enable,
            'pay_later_text' => $pay_later_text,
            'ride_tip' => [
                'visibility' => $show_status,
                'data' => [
                    'bottom_button_color' => $bottom_button_color,
                    'text_color' => $text_color,
                    'text' => trans("$string_file.tip"),
                    'action' => "TIP_BUTTON",
                    'payment_method_id' => $booking->payment_method_id,
                ]
            ]
        );
        return $this->successResponse(trans("$string_file.receipt"), $result);
    }

    public function driverReceipt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->where('booking_status', 1005);
                }),
            ],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
//            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $booking_data = new BookingDataController;
        $return_data = $booking_data->bookingReceiptForDriver($request);
        $driver = $request->user('api-driver');
        $string_file = $this->getStringFile(NULL, $driver->Merchant);
        return $this->successResponse(trans("$string_file.receipt"), $return_data);
    }

    public function cancelBookingByUSer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->wherein('booking_status', [1001, 1002, 1012, 1003, 1004, 1006]);
                }),
            ],
            'cancel_reason_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
//            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        DB::beginTransaction();
        try {
            $cancel_charges = $request->cancel_charges;
            $bookingData = new BookingDataController();
            $booking_id = $request->booking_id;
            $booking = Booking::with('Driver')->find($booking_id);
            $merchant_data = Merchant::find($booking->merchant_id);
            $string_file = $this->getStringFile(null, $booking->Merchant);
            $apply_charges = true;
            $free_km_for_cancel_charges = isset($merchant_data['free_distance_for_cancel_charges']) ? $merchant_data['free_distance_for_cancel_charges'] : 0;
            if (isset($merchant_data['cancel_charges_according_to_distance']) && $merchant_data['cancel_charges_according_to_distance'] == 1 && $booking->driver_id != '') {
                $bookingDetails = $booking->BookingDetail;
                $accept_lat = $bookingDetails->accept_latitude;
                $accept_long = $bookingDetails->accept_longitude;
                $driver_current_lat = $booking->Driver->current_latitude;
                $driver_current_long = $booking->Driver->current_longitude;
                $newDistance = new DistanceController();
                $driver_away_from_arrive = $newDistance->AerialDistanceBetweenTwoPoints($accept_lat, $accept_long, $driver_current_lat, $driver_current_long);
                if ($driver_away_from_arrive > $free_km_for_cancel_charges) {
                    $apply_charges = true;
                } else {
                    $apply_charges = false;
                }
            }
            if (!empty($cancel_charges) && $cancel_charges > 0 && $apply_charges == true) {
                if ($merchant_data['cancel_outstanding'] == 1):
                    $merchant = new \App\Http\Controllers\Helper\Merchant();
                    $payment = new CancelPayment();
                    $cancel_charges_received = $payment->MakePayment($booking, $booking->payment_method_id, $cancel_charges, $booking->user_id, $booking->card_id, $merchant_data->cancel_outstanding, $booking->driver_id);
                    $booking_transaction_submit = BookingTransaction::updateOrCreate([
                        'booking_id' => $booking_id,
                    ], [
                        'date_time_details' => date('Y-m-d H:i:s'),
                        'sub_total_before_discount' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                        'surge_amount' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                        'extra_charges' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                        'discount_amount' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                        'tax_amount' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                        'tip' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                        'insurance_amount' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                        'cancellation_charge_received' => $merchant->TripCalculation(isset($cancel_charges_received) ? $cancel_charges_received : '0.0', $booking->merchant_id),
                        'cancellation_charge_applied' => $merchant->TripCalculation($cancel_charges, $booking->merchant_id),
                        'toll_amount' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                        'cash_payment' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                        'online_payment' => $merchant->TripCalculation(isset($cancel_charges_received) ? $cancel_charges_received : '0.0', $booking->merchant_id),
                        'customer_paid_amount' => $merchant->TripCalculation(isset($cancel_charges_received) ? $cancel_charges_received : '0.0', $booking->merchant_id),
                        'company_earning' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                        'driver_earning' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                        'amount_deducted_from_driver_wallet' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                        'driver_total_payout_amount' => $merchant->TripCalculation($cancel_charges, $booking->merchant_id),
                        'trip_outstanding_amount' => $merchant->TripCalculation(($cancel_charges - (isset($cancel_charges_received) ? $cancel_charges_received : 0)), $booking->merchant_id),
                        'merchant_id'=>$booking->merchant_id
                    ]);
                    if (!empty($booking->driver_id)) {
                        $paramArray = array(
                            'merchant_id' => $booking->merchant_id,
                            'driver_id' => $booking->driver_id,
                            'booking_id' => $booking->id,
                            'amount' => $cancel_charges,
                            'narration' => 11,
                            'platform' => 1,
                            'payment_method' => 2,
                        );
                        WalletTransaction::WalletCredit($paramArray);
//                    \App\Http\Controllers\Helper\CommonController::WalletCredit($booking->driver_id,$booking->id,$cancel_charges,11,1,2);
                    }
                endif;
            }
            $booking->booking_status = 1006;
            $booking->booking_closure = 1;
            $booking->final_amount_paid = sprintf("%0.2f", $cancel_charges);
            $booking->cancel_reason_id = $request->cancel_reason_id;
            $booking->save();
            // inset booking status history
            $this->saveBookingStatusHistory($request, $booking, $booking->id);

            //payment option is payu then void the authorisation
            if ($booking->payment_method_id == 2) {
                $user_card = UserCard::find($booking->card_id);
                if ($user_card->PaymentOption->slug == "PAYU") {
                    $locale = $request->header('locale');
                    $this->payuVoid($booking, $locale);
                }
            }

            if ($booking->service_type_id == 5 && $booking->driver_id != '') {
                $poolBooking = new PoolController();
                $poolBooking->CancelRide($booking, $request);
            }
            // clear the call masking session
            $config = Configuration::where('merchant_id', $booking->merchant_id)->first();
            if (isset($config->twilio_call_masking) && $config->twilio_call_masking == 1) {
                TwilioMaskingHelper::close_session($booking);
            }
            if (!empty($booking->Driver)) {
                $booking->Driver->free_busy = 2;
                $booking->Driver->save();

//            $data = $bookingData->BookingNotification($booking);
//            $notification_data['notification_type'] ="CANCEL_BOOKING";
//            $notification_data['segment_type'] = $data['segment_type'];
//            $notification_data['segment_data']= $data;
//            $large_icon = $this->getNotificationLargeIconForBooking($booking);
//            $arr_param = ['driver_id'=>$booking->driver_id,'data'=>$notification_data,'message'=>$message,'merchant_id'=>$booking->merchant_id,'title'=>$title,'large_icon'=>$large_icon];
//            Onesignal::DriverPushMessage($arr_param);
                $booking_data_obj = new BookingDataController;
                $booking_data_obj->SendNotificationToDrivers($booking);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->failedResponse($exception->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.ride_cancelled"), $booking);
    }

    public function bookingAcceptReject(Request $request)
    {
        $driver = $request->user('api-driver');
        $driver_id = $driver->id;
        $booking_id = $request->id;
        $merchant_id = $driver->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $validator = Validator::make($request->all(), [
            'id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->whereIn('booking_status', array(1001, 1012));
                }),
                Rule::exists('booking_request_drivers', 'booking_id')->where(function ($query) use ($driver_id, $booking_id) {
                    $query->where('driver_id', $driver_id);
                }),
            ],
        ], [
            'exists' => trans("$string_file.already_accepted"),
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new Exception($errors[0]);
        }

        DB::beginTransaction();
        try {
            $accepted_driver_count = BookingRequestDriver::where([['booking_id', '=', $booking_id], ['request_status', '=', 2]])->get()->count();
            if ($accepted_driver_count > 0) {
                throw new Exception(trans("$string_file.ride_already"));
            }
//            $booking_obj = new Booking;
//            $booking = $booking_obj->getBooking($booking_id);


            $booking = Booking::select('id','user_id','final_amount_paid','family_member_id','booking_status_history','estimate_driver_distnace','estimate_distance','travel_distance','merchant_booking_id','booking_status','vehicle_type_id','driver_vehicle_id','price_card_id','ride_otp','ride_otp_verify','total_drop_location','booking_type','ploy_points','payment_method_id','vehicle_type_id','driver_id','merchant_id','segment_id','pickup_location','country_area_id','driver_id','user_id','pickup_latitude','pickup_longitude','service_type_id','additional_notes','family_member_id','onride_waiting_type','waypoints','drop_latitude','drop_longitude','drop_location','payment_status','estimate_bill','travel_time','country_area_id','booking_closure','later_booking_date','later_booking_time','unique_id','map_image','platform','user_masked_number','driver_masked_number')
                ->with(['User' => function ($query) {
                    $query->select('id','country_id', 'merchant_id', 'first_name', 'last_name', 'UserPhone', 'email', 'rating', 'UserProfileImage','rating');
                }])
                ->with(['Driver' => function ($query) {
                    $query->select('id', 'first_name','current_latitude','country_area_id','current_longitude','last_location_update_time','driver_gender', 'last_name', 'email', 'phoneNumber', 'profile_image', 'rating','ats_id');
                }])
                ->with(['PaymentMethod' => function ($query) {
                    $query->select('id', 'payment_method', 'payment_icon');
                }])
                ->with(['VehicleType' => function ($query) {
                    $query->select('id', 'vehicleTypeImage','rating');
                }])
                ->with(['ServiceType' => function ($query) {
                    $query->select('id', 'serviceName','type');
                }])
                ->orderBy('created_at','DESC')
                ->sharedLock()->find($booking_id);

            // status == 1 means accept request
            if ($request->status == "ACCEPT") {
                // delete users checkout at accept screen
                BookingCheckout::where([['merchant_id','=',$booking->merchant_id],['user_id','=',
                    $booking->user_id],['country_area_id','=',
                    $booking->country_area_id]])->delete();
                $driver->free_busy = 1;
                $driver->save();
                $ployline = "";
                $booking->ride_otp_verify = 2;
                $message_otp = '';
                if ($booking->Merchant->BookingConfiguration->ride_otp == 1):
                    $booking->ride_otp = rand(1000, 9999);
                    $booking->ride_otp_verify = 1;
                    // send otp on whatsapp message
                    if ($booking->platform == 3) {
                        $message_otp = '. ' . trans("$string_file.ride_start_otp") . ' ' . trans("$string_file.id") . ' #' . $booking->merchant_booking_id . ': ' . $booking->ride_otp;
                    }
                endif;
                $booking_status = $booking->booking_status;
                // If booking accept by taxi company driver
                if ($driver->taxi_company_id != NULL && $booking->Merchant->Configuration->company_admin == 1) {
                    $booking->taxi_company_id = $driver->taxi_company_id;
                }
                $booking->booking_status = 1002;
                $booking->driver_vehicle_id = $request->driver_vehicle_id; // coming from store online config
                $booking->driver_id = $driver_id;
                $booking->unique_id = uniqid();
                $booking->ploy_points = $ployline;
                $booking->save();
                $merchant_id = $booking->merchant_id;

                // ride accepted driver's player id
                // inset booking status history
                $this->saveBookingStatusHistory($request, $booking, $booking->id);

                if ($booking->service_type_id == 5) {
                    $poolBooking = new PoolController();
                    $poolBooking->AcceptRide($booking, $request);
                }
                BookingRequestDriver::where([['booking_id', '=', $booking_id], ['driver_id', '=', $driver_id]])->update(['request_status' => 2]);
                $cancelDriver = BookingRequestDriver::with(['Driver' => function ($q) use ($driver) {
                    return $q->select("*", "id as driver_id")->where('player_id', '!=', $driver->player_id);
                }])
                    ->whereHas('Driver', function ($q) use ($driver) {
                        return $q->where('player_id', '!=', $driver->player_id);
                    })->where([['booking_id', '=', $booking_id], ['request_status', '=', 1]])->get();
                $cancelDriver = array_pluck($cancelDriver, 'Driver');
                $bookingData = new BookingDataController();

                foreach ($cancelDriver as $key => $value) {
                    $newDriver = Driver::select('id', 'last_ride_request_timestamp')->find($value->id);
                    $newDriver->last_ride_request_timestamp = date("Y-m-d H:i:s", time() - 100);
                    $newDriver->save();
                }
//                $user_id = $booking->user_id;
                BookingDetail::create([
                    'booking_id' => $booking_id,
                    'accept_timestamp' => strtotime('now'),
                    'accept_latitude' => $request->latitude,
                    'accept_longitude' => $request->longitude,
                    'accuracy_at_accept' => $request->accuracy,
                ]);

                DB::commit();

                if (!empty($cancelDriver)) {
                    $bookingData->SendNotificationToDrivers($booking, $cancelDriver);
                }

//                $message = $booking_status == 1001 ? $bookingData->LanguageData($booking->merchant_id, 27) : $bookingData->LanguageData($booking->merchant_id, 28);
                $bookingData = new BookingDataController();
                $notification_data = $bookingData->bookingNotificationForUser($booking, "ACCEPT_BOOKING", $message_otp);
//                Onesignal::UserPushMessage($user_id, $notification_data, $message, 1, $booking->merchant_id);

                // mask the number
                $config = Configuration::where('merchant_id', $booking->merchant_id)->first();
                if (isset($config->twilio_call_masking) && $config->twilio_call_masking == 1) {
                    $expiry_in_seconds = 60 * 60 * 6; // half day
                    $booking = TwilioMaskingHelper::mask_numbers($booking, $expiry_in_seconds, $driver_id);
                }

                $booking = $bookingData->driverBookingDetails($booking_id, true, $request);
                $SmsConfiguration = SmsConfiguration::select('ride_accept_enable')->where([['merchant_id', '=', $merchant_id]])->first();
                if (!empty($SmsConfiguration) && $SmsConfiguration->ride_accept_enable == 2) {
                    $sms = new SmsController();
                    $phone = $booking->User->UserPhone;
                    $sms->SendSms($merchant_id, $phone, null, 'RIDE_ACCEPT', $booking->User->email);
                }
                $message = trans("$string_file.accepted");
            } elseif ($request->status == "REJECT") {
                // status 2 means reject the booking
                BookingRequestDriver::where([['booking_id', '=', $booking_id], ['driver_id', '=', $driver_id]])->update(['request_status' => 3]);
                $driver->last_ride_request_timestamp = date("Y-m-d H:i:s", time() - 100);
                $driver->save();

                $limit = getSendDriverRequestLimit($booking);
                $bookingData = new BookingDataController();
                if ($limit == 1) {
                    $bookingData->sendRequestToNextDrivers($booking_id, 1, 'auto_upgrade');
                } elseif ($limit > 1) {
                    $bookingRequestDriversCount = BookingRequestDriver::where('booking_id', $booking_id)->count();
                    $noActionAndRejectDriversCount = BookingRequestDriver::where([['booking_id', $booking_id], ['request_status', 3]])->count();
                    if ($bookingRequestDriversCount == $noActionAndRejectDriversCount) {
                        $bookingData->sendRequestToNextDrivers($booking_id, 1, 'auto_upgrade');
                    }
                }
                DB::commit();

                $message = trans("$string_file.ride_rejected");
                $booking = [];
            }
            BookingRequestDriver::where([['booking_id', '=', $booking_id], ['driver_id', '=', $driver_id]])->update(['inside_function' => NULL]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return ['message' => $message, 'data' => $booking];
    }

    // partial booking
    public function acceptUpcomingBooking(Request $request)
    {
        $driver = $request->user('api-driver');
        $validator = Validator::make($request->all(), [
            'booking_id' => ['required', 'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->where([['booking_status', '=', 1001], ['booking_type', '=', 2]]);
                }),
            ],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $booking_id = $request->booking_id;
            $booking = Booking::select('id', 'estimate_time', 'booking_status_history', 'later_booking_date', 'later_booking_time', 'merchant_booking_id', 'payment_method_id', 'vehicle_type_id', 'driver_id', 'merchant_id', 'segment_id', 'country_area_id', 'driver_id', 'user_id', 'service_package_id', 'service_type_id', 'is_geofence', 'base_area_id', 'auto_upgradetion', 'number_of_rider', 'total_drop_location', 'price_card_id', 'driver_vehicle_id', 'family_member_id', 'pickup_latitude', 'pickup_longitude', 'pickup_location', 'drop_latitude', 'drop_longitude', 'drop_location', 'booking_type', 'estimate_bill', 'additional_information', 'additional_notes')->find($booking_id);
            $string_file = $this->getStringFile($booking->merchant_id);
            $booking_later_booking_date_time = $booking->later_booking_date." ".$booking->later_booking_time;
            $current_DateTime = convertTimeToUSERzone(date('Y-m-d H:i'), $booking->CountryArea->timezone,null,$booking->Merchant);

//            if ($booking->later_booking_date <= date('Y-m-d') && $booking->later_booking_time <= date('H:i')) {
            if (strtotime($booking_later_booking_date_time) <= strtotime($current_DateTime)) {
                $booking_status = "1016";
                $booking->save();
                // inset booking status history
                $this->saveBookingStatusHistory($request, $booking, $booking->id);
                $data = array('booking_id' => $booking->id, 'booking_status' => $booking_status);
                return $this->failedResponse(trans("$string_file.ride_expired"), $data);
            }
            $driverPartial = Booking::where([['driver_id', '=', $driver->id], ['booking_status', '=', 1012]])->get();
            $time = $booking->Merchant->BookingConfiguration->partial_accept_hours;
            $partial_accept_before_minutes = $booking->Merchant->BookingConfiguration->partial_accept_before_hours;
            $bookingTimeString = $booking->later_booking_date . " " . $booking->later_booking_time;

            // convert to date object
            $bookingTime = new DateTime($bookingTimeString);
            $endbookingTime = new DateTime($bookingTimeString);

            // check for partial accept before hours
//            $current_DateTime = new \DateTime();
//            $minutes = (strtotime($bookingTime->format("Y-m-d H:i")) - strtotime($current_DateTime->format("Y-m-d H:i"))) / 60;

            // current time according to time zone
            $current_DateTime = convertTimeToUSERzone(date('Y-m-d H:i'), $booking->CountryArea->timezone,null,$booking->Merchant);
            $minutes = (strtotime($bookingTime->format("Y-m-d H:i")) - strtotime($current_DateTime)) / 60;
            if ($minutes > $partial_accept_before_minutes) {
                $config_time = date(getDateTimeFormat(2), strtotime('-' . $partial_accept_before_minutes . 'minutes', strtotime($bookingTime->format("Y-m-d H:i:s"))));
                $message = trans("$string_file.ride_later_accept_warning") . $config_time;
                return $this->failedResponse($message);
            }

            // add estimate time + time difference between ride
            $endbookingTime->modify("+{$booking->estimate_time}");
            $endbookingTime->modify("+{$time} mins");

            // convert to time string
            $bookingTime = $bookingTime->format("Y-m-d H:i");
            $endbookingTime = $endbookingTime->format("Y-m-d H:i");

            foreach ($driverPartial as $value) {
                $bookingtimestamp = $value->later_booking_date . " " . $value->later_booking_time;

                // Accepted ride time to date object
                $DateTime = new DateTime($bookingtimestamp);
                $oldDateTime = new DateTime($bookingtimestamp);
                if ($value->estimate_time) {
                    // add estimate time to date object
                    $oldDateTime->modify("+{$value->estimate_time}");
                }

                // add time difference between ride
                $oldDateTime->modify("+{$time} mins");
                // convert to time string
                $newDate = $DateTime->format("Y-m-d H:i");
                $oldDate = $oldDateTime->format("Y-m-d H:i");

                // Condition check for active ride or booking ride time and date conflicts
                if ($bookingTime >= $oldDate && $endbookingTime >= $newDate || $bookingTime <= $oldDate && $endbookingTime <= $newDate) {
                    continue;
                } else {
                    return $this->failedResponse(trans("$string_file.already_activated_booking", ['time' => $bookingtimestamp]));

                }
            }
            // If booking accept by taxi company driver
            if ($driver->taxi_company_id != NULL && $booking->Merchant->Configuration->company_admin == 1) {
                $booking->taxi_company_id = $driver->taxi_company_id;
            }
            $booking->booking_status = 1012;
            $booking->driver_vehicle_id = $request->driver_vehicle_id; // coming from store online config
            $booking->driver_id = $driver->id;
            $booking->save();

            // inset booking status history
            $this->saveBookingStatusHistory($request, $booking, $booking->id);

            //$user_id = $booking->user_id;
            $bookingData = new BookingDataController();
            //$message = $bookingData->LanguageData($booking->merchant_id, 30);
            // $data = $bookingData->BookingNotification($booking);
            BookingRequestDriver::create([
                'booking_id' => $request->booking_id,
                'driver_id' => $driver->id,
                'distance_from_pickup' => 0,
                'request_status' => 1
            ]);
            $notification_data = $bookingData->bookingNotificationForUser($booking, "PARTIAL_ACCEPTED");
            //Onesignal::UserPushMessage($user_id, $data, $message, 1, $booking->merchant_id);
            $booking = $bookingData->DriverBookingDetails($booking_id, $booking->merchant_id, $request);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.ride_accepted"), $booking);
    }

    public function arrivedAtPickup(Request $request)
    {
        $merchant_id = $request->user('api-driver')->merchant_id;
        $validator = Validator::make($request->all(), [
            'id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->where('booking_status', 1002);
                }),
            ],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new Exception($errors[0]);
        }
        DB::beginTransaction();
        try {
            $booking_id = $request->id;
            $booking_obj = new Booking;
            $booking = $booking_obj->getBooking($booking_id);
            $booking->booking_status = 1003;
            $booking->save();
            $string_file = $this->getStringFile($booking->Merchant);
            // inset booking status history
            $this->saveBookingStatusHistory($request, $booking, $booking->id);

            $user_id = $booking->user_id;
            BookingDetail::where([['booking_id', '=', $booking_id]])->update([
                'arrive_timestamp' => strtotime('now'),
                'arrive_latitude' => $request->latitude,
                'arrive_longitude' => $request->longitude,
                'accuracy_at_arrive' => $request->accuracy,
            ]);
            $bookingData = new BookingDataController();
            // $message = $bookingData->LanguageData($booking->merchant_id, 31);
            $notification_data = $bookingData->bookingNotificationForUser($booking, "ARRIVED_AT_PICKUP");
            //Onesignal::UserPushMessage($user_id, $notification_data, $message, 1, $booking->merchant_id);
            $return_data = $bookingData->driverBookingDetails($booking, false, $request);
            $booing_config = BookingConfiguration::where('merchant_id', $booking->merchant_id)->first();
//            if ($booking->Segment->slag == "DELIVERY" && isset($booing_config->delivery_drop_otp) && ($booing_config->delivery_drop_otp == 1 || $booing_config->delivery_drop_otp == 2)) {
//                // Store Delivery Details
//                $this->storeDeliveryDetails($booking);
//            }
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        DB::commit();
        return ['message' => trans("$string_file.arrived_pickup"), 'data' => $return_data];
    }

    // booking start by driver
    public function startBooking(Request $request)
    {
        $driver = $request->user('api-driver');
        $merchant_id = $driver->merchant_id;
        $string_file = $this->getStringFile(NULL, $driver->Merchant);
        $validator = Validator::make($request->all(), [
            'id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->where('booking_status', 1003);
                }),
            ],
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new Exception($errors[0]);
        }
        DB::beginTransaction();
        try {
            $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
            $key = $configuration->google_key;
            $booking_id = $request->id;
            $booking_update = Booking::select('id', 'booking_status_history', 'booking_status')->Find($booking_id);
            $booking_update->booking_status = 1004;
            $booking_update->save();

            // inset booking status history
            $this->saveBookingStatusHistory($request, $booking_update, $booking_update->id);

            // get booking data
            $booking_obj = new Booking;
            $booking = $booking_obj->getBooking($booking_id);

            $country_area_id = $booking->country_area_id;
            $service_type_id = $booking->service_type_id;
            $service_type = $booking->ServiceType->type;
            // require for services that type is rental or outstation
            if (in_array($service_type, array(2, 4))) {
                $validator = Validator::make($request->all(), [
                    'send_meter_image' => 'required',
                    'send_meter_value' => 'required|numeric|min:0',
                ]);
                if ($validator->fails()) {
                    $errors = $validator->messages()->all();
                    throw new Exception($errors[0]);
                }
            }
            $price_card_id = $booking->price_card_id;
            $user_id = $booking->user_id;
            $startAddress = GoogleController::GoogleLocation($request->latitude, $request->longitude, $key, 'rideStart', $string_file);
            $startAddress = $startAddress ? $startAddress : 'Address Not found';
            $bookingDetails = BookingDetail::where([['booking_id', '=', $booking_id]])->first();
            $newDistance = new DistanceController();
            $dead_milage_distance = $newDistance->AerialDistanceBetweenTwoPoints($request->latitude, $request->longitude, $bookingDetails->accept_latitude, $bookingDetails->accept_longitude);
            $arriveTimeStemp = $bookingDetails->arrive_timestamp;
            $startTimeStemp = strtotime('now');
            $waitTime = round(abs($arriveTimeStemp - $startTimeStemp) / 60, 2);
            if (in_array($service_type, array(2, 4))) {
                $bookingDetails->start_meter_value = $request->send_meter_value;
                $bookingDetails->start_meter_image = $this->uploadBase64Image('send_meter_image', 'send_meter_image', $merchant_id);
//                    $this->uploadImage('send_meter_image', 'service', $merchant_id);
            }
            if ($service_type_id == 5) {
                PoolRideList::where([['driver_id', '=', $booking->driver_id], ['booking_id', '=', $request->booking_id]])->update(['pickup' => 1]);
            }
            $bookingDetails->start_timestamp = $startTimeStemp;
            $bookingDetails->dead_milage_distance = $dead_milage_distance;
            $bookingDetails->start_latitude = $request->latitude;
            $bookingDetails->start_longitude = $request->longitude;
            $bookingDetails->accuracy_at_start = $request->accuracy;
            $bookingDetails->start_location = $startAddress;
            $bookingDetails->wait_time = $waitTime;
            $bookingDetails->save();
            $config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
            $bookingData = new BookingDataController();

            if (!empty($config->toll_api)) {
                $newTool = new Toll();
                $toolPrice = $newTool->checkToll($config->toll_api, $startAddress, $booking->drop_location, $booking->waypoints, $config->toll_key);
                if (is_array($toolPrice) && array_key_exists('cost', $toolPrice) && $toolPrice['cost'] > 0) {

                    $notification_data = $bookingData->bookingNotificationForUser($booking, "TOLL_ADDED");

                }
            }
            //send notification to user
//            $notification_data['notification_type'] = "RIDE_START";
            $notification_data = $bookingData->bookingNotificationForUser($booking, "RIDE_START");
            // Onesignal::UserPushMessage($user_id, $notification_data, $message, 1, $booking->merchant_id);
            $return_data = $bookingData->driverBookingDetails($booking, false, $request);
            $SmsConfiguration = SmsConfiguration::select('ride_start_enable', 'ride_start_msg')->where([['merchant_id', '=', $merchant_id]])->first();
            if ($SmsConfiguration && $SmsConfiguration->ride_start_enable == 1) {
                $sms = new SmsController();
                $phone = $booking->User->UserPhone;
                $sms->SendSms($merchant_id, $phone, null, 'RIDE_START', $booking->User->email);
            }
            $commonController = new CommonController();
            $commonController->geofenceDequeue($request->latitude, $request->longitude, $driver, $country_area_id);
            $deliveryController = new DeliveryController();
            $deliveryController->sendOtpToReceiver($booking->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        DB::commit();
        return ['message' => trans("$string_file.ride_started"), 'data' => $return_data];
    }

//     reach
    public function reachedAtMultiDrop(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->where('booking_status', 1004);
                }),
            ],
            'latitude' => 'required',
            'longitude' => 'required',
            'segment_slug' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::rollBack();
        try {
            $booking_id = $request->booking_id;
            $booking_obj = new Booking;
            $booking = $booking_obj->getBooking($booking_id);
            $string_file = $this->getStringFile(NULL, $booking->Merchant);
            $drop_location = $booking->waypoints;
            $multiple_location = json_decode($drop_location, true);
            $final = [];
            $done = '';
            $booking_config = $booking->Merchant->BookingConfiguration;
            $delivery_drop_otp = false;
            $delivery_drop_qr = false;
            if ($request->segment_slug == "DELIVERY" && isset($booking_config->delivery_drop_otp)) {
                $validate_arr = [
                    'receiver_otp' => 'required',
                ];
                if ($booking_config->delivery_drop_otp == 1) {
                    $delivery_drop_otp = true;
                    $validate_arr = array_merge($validate_arr, array(
                        'receiver_otp' => 'required',
                        'receiver_name' => 'required',
                        'receiver_image' => 'required',
                    ));
                    $validator = Validator::make($request->all(), $validate_arr);
                }
                if ($booking_config->delivery_drop_otp == 2) {
                    $delivery_drop_qr = true;
                    $validator = Validator::make($request->all(), $validate_arr);
                }
                if ($delivery_drop_otp || $delivery_drop_qr) {
                    if ($validator->fails()) {
                        $errors = $validator->messages()->all();
                        return $this->failedResponse($errors[0]);
                    }
                }
            }
            $done = true;
            foreach ($multiple_location as $key => $val) {
                if ($val['status'] == 1 && $done):
                    if ($delivery_drop_otp || $delivery_drop_qr) {
                        $booking_delivery_detail = BookingDeliveryDetails::where(['booking_id' => $booking->id, 'stop_no' => $val['stop']])->first();
                        if (!empty($booking_delivery_detail)) {
                            // have to manage in booking data controller
                            // get next stop data
                            $next_stop = $val['stop'] + 1;
                            $booking_delivery_detail_next = BookingDeliveryDetails::select('additional_notes', 'product_image_one', 'product_image_two')->where(['booking_id' => $booking->id, 'stop_no' => $next_stop])->first();
                            $request->request->add(["additional_notes" => $booking_delivery_detail_next->additional_notes, 'product_image_one' => $booking_delivery_detail_next->product_image_one, 'product_image_two' => $booking_delivery_detail_next->product_image_two]);

                            if ($request->receiver_otp == $booking_delivery_detail->opt_for_verify) {
                                $booking_delivery_detail->otp_status = 1;
                                $booking_delivery_detail->drop_status = 1;
                                $booking_delivery_detail->receiver_name = $request->receiver_name;
                                $booking_delivery_detail->receiver_phone = isset($request->receiver_name) ? $request->receiver_name : '';
                                if (!empty($request->receiver_image)) {
                                    $booking_delivery_detail->receiver_image = $this->uploadImage('receiver_image', 'booking_images', $booking->merchant_id);
                                }
                                $booking_delivery_detail->drop_latitude = $request->latitude;
                                $booking_delivery_detail->drop_latitude = $request->longitude;
                                $booking_delivery_detail->save();
                            } else {
                                return $this->failedResponse(trans("$string_file.invalid_otp_try_again"), []);
                            }
                        }
                    }
                    $val['status'] = 2;
                    $val['end_latitude'] = $request->latitude;
                    $val['end_longitude'] = $request->longitude;
                    $val['end_time'] = date('Y-m-d H:i:s');
                    $done = false;
                endif;
                array_push($final, $val);
            }
            $updated_location = json_encode($final);
            $booking->waypoints = $updated_location;
            $booking->save();

            $bookingData = new BookingDataController();
            //$message = $bookingData->LanguageData($booking->merchant_id, 33);
//        $data = $bookingData->BookingNotification($booking);
//        Onesignal::UserPushMessage($booking->user_id, $data, $message, 1, $booking->merchant_id);

            $bookingData->bookingNotificationForUser($booking, "REACH_AT_DROP");
            $booking = $bookingData->DriverBookingDetails($request->booking_id, $booking->merchant_id, $request);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.reached_drop"), $booking);
    }

    public function addTip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id'),
            ],
            'tip_amount' => 'required|numeric|min:1',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $booking = Booking::select("id", "country_area_id", "driver_id", "booking_status", "payment_status", "final_amount_paid", "bill_details", "segment_id", "merchant_id", "payment_method_id", "user_id")->Find($request->booking_id);
            $string_file = $this->getStringFile(NULL, $booking->Merchant);
            $bookingDetails = $booking->BookingDetail;
            if ($bookingDetails->tip_amount > 0) {
                $message = trans("$string_file.tip_already_added");
                return $this->failedResponse($message);
            }
            $bookingDetails->tip_amount = ($request->tip_amount > 0) ? $request->tip_amount : 0;
            $bookingDetails->save();
            if ($booking->booking_status == 1005 && $booking->payment_status == 1) {
                $tip_amount = $request->tip_amount;
                // make payment calls if payment method is not cash
                if ($booking->payment_method_id != 1) {
                    // make payment
                    $currency = $booking->CountryArea->Country->isoCode;
                    $array_param = array(
                        'booking_id' => NULL, // we don't want to update any status thats why booking id is going as null
                        'payment_method_id' => $booking->payment_method_id,
                        'amount' => $tip_amount,
                        'user_id' => $booking->user_id,
                        'card_id' => $booking->card_id,
                        'currency' => $currency,
                        'quantity' => 1,
                        'order_name' => $booking->merchant_booking_id,
                        'driver_sc_account_id' => $booking->Driver->sc_account_id
                    );

                    $payment = new Payment();
                    $payment->MakePayment($array_param);

                    // credit driver wallet
                    $paramArray = array(
                        'driver_id' => $booking->driver_id,
                        'booking_id' => $booking->id,
                        'order_id' => NULL,
                        'handyman_order_id' => NULL,
                        'amount' => $tip_amount,
                        'narration' => 16,
                    );
                    WalletTransaction::WalletCredit($paramArray);
                }

                // update booking amount
                // $booking->final_amount_paid = $booking->final_amount_paid + $tip_amount;
                // $booking->save();
                $arr_bill_details = json_decode($booking->bill_details, true);
                $updated_bill_details = [];
                foreach ($arr_bill_details as $bill_details) {
                    $new_bill_details = $bill_details;
                    if (isset($bill_details['parameterType']) && $bill_details['parameterType'] == "tip_amount") {
                        $new_bill_details['amount'] = $tip_amount;
                    }

                    $updated_bill_details[] = $new_bill_details;
                }
                $booking->final_amount_paid = $booking->final_amount_paid + $tip_amount;
                $booking->bill_details = json_encode($updated_bill_details);
                $booking->save();

                // booking details
                $bookingDetails->total_amount = $bookingDetails->total_amount + $tip_amount;
                $bill_details = json_decode($bookingDetails->bill_details, true);
                $updated_details = [];
                foreach ($bill_details as $details) {
                    $new_bill_details = $details;
                    if (isset($details['parameterType']) && $details['parameterType'] == "tip_amount") {
                        $new_bill_details['amount'] = $tip_amount;
                    }
                    $updated_details[] = $new_bill_details;
                }
                $bookingDetails->bill_details = json_encode($updated_details);
                $bookingDetails->save();

                // update total amount of driver in transaction
                $booking_transaction = $booking->BookingTransaction;
                $driver_existing_amount = $booking_transaction->driver_total_payout_amount;
                $existing_booking_amount = $booking_transaction->customer_paid_amount;
                $existing_online_payment = $booking_transaction->online_payment;
                $existing_cash_payment = $booking_transaction->cash_payment;

                $booking_transaction->tip = $tip_amount;
                $booking_transaction->driver_total_payout_amount = $driver_existing_amount + $tip_amount;
                $booking_transaction->customer_paid_amount = $existing_booking_amount + $tip_amount;
                if ($booking->payment_method_id == 1) {
                    $booking_transaction->cash_payment = $existing_cash_payment + $tip_amount;
                } else {
                    $booking_transaction->online_payment = $existing_online_payment + $tip_amount;
                }
                $booking_transaction->save();

                // tip credited notification
                setLocal($booking->Driver->language);
                $data = array('notification_type' => 'TIP_ADDED', 'segment_type' => $booking->Segment->slag, 'segment_data' => []);
                $arr_param = array(
                    'driver_id' => $booking->driver_id,
                    'data' => $data,
                    'message' => trans("$string_file.tip_credited_to_driver"),
                    'merchant_id' => $booking->merchant_id,
                    'title' => trans("$string_file.tip_credited")
                );
                Onesignal::DriverPushMessage($arr_param);
                setLocal();
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.tip_message"), []);
    }

    public function EstimateBillDetailsBreakup($billDetails_array, $estimate_bill)
    {
        $billDetails_array = json_decode($billDetails_array, true);
        $promocode_array = array_filter($billDetails_array, function ($e) {
            if ((isset($e['parameterType']) && $e['parameterType'] == "PROMO CODE") || ($e['parameter'] == "Promotion")) {
                return (($e['parameterType'] == "PROMO CODE") || ($e['parameter'] == "Promotion"));
            }
        });
        $promocode_discount = '0.0';
        if (!empty($promocode_array)):
            $promocode_discount = array_sum(Arr::pluck($promocode_array, 'amount'));
        endif;

        $cancellation_array = array_filter($billDetails_array, function ($e) {
            return (array_key_exists('parameter', $e) ? $e['parameter'] == "Cancellation fee" : []);
        });
        $cancellation_amount_received = '0.0';
        if (!empty($cancellation_array)):
            $cancellation_amount_received = array_sum(Arr::pluck($cancellation_array, 'amount'));
        endif;

        $distance_charge_array = array_filter($billDetails_array, function ($e) {
            return (array_key_exists('parameterType', $e) ? $e['parameterType'] == "1" : []);
        });
        $distance_amount_received = '0.0';
        if (!empty($distance_charge_array)):
            $distance_amount_received = array_sum(Arr::pluck($distance_charge_array, 'amount'));
        endif;

        $min_charge_array = array_filter($billDetails_array, function ($e) {
            return (array_key_exists('parameterType', $e) ? $e['parameterType'] == "8" : []);
        });
        $min_amount_received = '0.0';
        if (!empty($min_charge_array)):
            $min_amount_received = array_sum(Arr::pluck($min_charge_array, 'amount'));
        endif;

        $hr_charge_array = array_filter($billDetails_array, function ($e) {
            return (array_key_exists('parameterType', $e) ? $e['parameterType'] == "2" : []);
        });
        $hr_amount_received = '0.0';
        if (!empty($hr_charge_array)):
            $hr_amount_received = array_sum(Arr::pluck($hr_charge_array, 'amount'));
        endif;

        $amount_without_spl_discount_array = array_filter($billDetails_array, function ($e) {
            return (array_key_exists('simply_amount', $e) ? $e['simply_amount'] == "amount_without_spl_discount" : []);
        });
        $amount_without_spl_discount = '0.0';
        if (!empty($amount_without_spl_discount_array)):
            $amount_without_spl_discount = array_sum(Arr::pluck($amount_without_spl_discount_array, 'amount'));
        endif;

        $surge_array = array_filter($billDetails_array, function ($e) {
            return (array_key_exists('parameter', $e) ? $e['parameter'] == "Surge-Charge" : []);
        });
        $surge = '0.0';
        if (!empty($surge_array)):
            $surge = array_sum(Arr::pluck($surge_array, 'amount'));
        endif;

        $extra_charge = '0.0';
        $extra_charge_array = array_filter($billDetails_array, function ($e) {
            return (array_key_exists('extra_charges_amount', $e) ? $e['extra_charges_amount'] == "Extra-Charges" : []);
        });
        if (!empty($extra_charge_array)):
            $extra_charge = array_sum(Arr::pluck($extra_charge_array, 'amount'));
        endif;

        $amount_without_discount = ($amount_without_spl_discount + $surge + $extra_charge + $distance_amount_received + $hr_amount_received + $min_amount_received);

        $toll_charge = '0.0';
        $toll_charge_array = array_filter($billDetails_array, function ($e) {
            return (array_key_exists('parameter', $e) ? $e['parameter'] == "TollCharges" : []);
        });
        if (!empty($toll_charge_array)):
            $toll_charge = array_sum(Arr::pluck($toll_charge_array, 'amount'));
        endif;

        $tax_charge = '0.0';
        $tax_charge_array = array_filter($billDetails_array, function ($e) {
            return (array_key_exists('type', $e) ? $e['type'] == "TAXES" : []);
        });
        if (!empty($tax_charge_array)):
            $tax_charge = array_sum(Arr::pluck($tax_charge_array, 'amount'));
        endif;


        $insurance_amount_array = array_filter($billDetails_array, function ($e) {
            return (array_key_exists('parameter', $e) ? $e['parameter'] == "Insurance" : []);
        });
        $insurance_amount = '0.0';
        if (!empty($insurance_amount_array)):
            $insurance_amount = array_sum(Arr::pluck($insurance_amount_array, 'amount'));
        endif;

        $bookingFeeArray = array_filter($billDetails_array, function ($e) {
            return (array_key_exists('parameterType', $e) ? $e['parameterType'] == "17" : []);
        });
        $bookingFee = '0.0';
        if (!empty($bookingFeeArray)):
            $bookingFee = array_sum(Arr::pluck($bookingFeeArray, 'amount'));
        endif;

        $hotel_amount_array = array_filter($billDetails_array, function ($e) {
            return (array_key_exists('parameter', $e) ? $e['parameter'] == "Hotel Charges" : []);
        });
        $hotel_amount = '0.0';
        if (!empty($hotel_amount_array)):
            $hotel_amount = array_sum(Arr::pluck($hotel_amount_array, 'amount'));
        endif;



        return [
            'bill_details' => $billDetails_array,
            'amount' =>  $estimate_bill >= $promocode_discount ? $estimate_bill - $promocode_discount : $estimate_bill,
            'promo' => $promocode_discount,
            'cancellation_amount_received' => $cancellation_amount_received,
            'subTotalWithoutSpecial' => $amount_without_spl_discount,
            'subTotalWithoutDiscount' => $amount_without_discount,
            'toolCharge' => $toll_charge,
            'surge' => $surge,
            'extracharge' => $extra_charge,
            'insurnce_amount' => $insurance_amount,
            'total_tax' => $tax_charge,
            'booking_fee' => $bookingFee,
            'hotel_amount' => $hotel_amount,
        ];
    }

    public function SubscriptionPackageExpiryCheck(Driver $driver)
    {
        // find free package if assigned to driver
        $free_package = DriverSubscriptionRecord::select('package_total_trips', 'id', 'used_trips')->where([['package_type', 1], ['driver_id', $driver->id], ['status', 2], ['end_date_time', '>', date('Y-m-d H:i:s')]])->orderBy('id', 'DESC')->first();
        $free_package_exist = false;
        if (!empty($free_package->id) && $free_package->used_trips < $free_package->package_total_trips) {
            $free_package_exist = true;
            $free_package->used_trips = $free_package->used_trips + 1;
            $free_package->save();
        } else {
            // if there is no free package or used all free ride then check paid package of driver and deduct ride from that package
            // elseif($free_package_exist == false)
            $paid_package = DriverSubscriptionRecord::select('package_total_trips', 'id', 'used_trips')->where([['package_type', 2], ['driver_id', $driver->id], ['status', 2], ['end_date_time', '>', date('Y-m-d H:i:s')]])->orderBy('id', 'DESC')->first();
            if (!empty($paid_package->id) && $paid_package->used_trips < $paid_package->package_total_trips) {
                $paid_package->used_trips = $paid_package->used_trips + 1;
                $paid_package->save();
            }
        }
        if (($free_package_exist == true && $free_package->used_trips >= $free_package->package_total_trips) || (!empty($paid_package->id) && $free_package_exist == false && $paid_package->used_trips >= $paid_package->package_total_trips)) :
            $driver->online_offline = 2;
            $driver->save();
            $data = [];
            setLocal($driver->language);
            /// code commented for string correction
//            $message = trans('api.subscription_package_expire');
//            Onesignal::DriverPushMessage($driver->id, $data, $message, 17, $driver->merchant_id);
            setLocal();
        endif;
    }

    // driver booking info, while ride is going
    public function driverBookingInfo(Request $request) // FOR DRIVER SIDE
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:bookings,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new Exception($errors[0]);
        }
        try {
            $bookingData = new BookingDataController();
            $return_data = $bookingData->driverBookingDetails($request->id, true, $request);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $return_data;
    }

    //
    public function bookingDetails(Request $request) // FOR USER SIDE
    {
        $merchant_id = $request->user('api')->merchant_id;
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|integer|exists:bookings,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $booking_id = $request->booking_id;
            $booking_obj = new Booking;
            $booking = $booking_obj->getBooking($booking_id);
            $show_status = 2;
            if (!empty($booking->BookingDetail)) {
                if ($booking->BookingDetail->tip_amount > 0) {
                    $show_status = 1;
                }
            }
            // Waiting time timer for user app
            $price_card = PriceCard::with(['PriceCardValues' => function ($query) {
                $query->with(['PricingParameter' => function ($q) {
                    $q->where('parameterType', 9);
                }]);
            }])->find($booking->price_card_id);
            $free_value = '';
            if (isset($price_card) && !empty($price_card->PriceCardValues)) {
                foreach ($price_card->PriceCardValues as $pcv) {
                    if (isset($pcv->PricingParameter)) {
                        $parameter = $pcv->PricingParameter;
                        if (!empty($parameter)) {
                            $free_value = $pcv->free_value;
                        }
                    }
                }
            }
            $booking->free_waiting_time_enable = !empty($free_value) ? true : false;
            $booking->free_waiting_time = $free_value;

            $merchant = Merchant::select('id')->find($merchant_id);
            if ($merchant->Configuration->twilio_call_masking == 1) {
                $booking->driver->phoneNumber = $booking->driver_masked_number ?? '';
            }
            $booking->PaymentMethod->payment_method = $booking->PaymentMethod->MethodName($merchant_id) ? $booking->PaymentMethod->MethodName($merchant_id) : $booking->PaymentMethod->payment_method;
            $app_config = $merchant->ApplicationConfiguration;
            $config = $merchant->BookingConfiguration;
            $vehicleDetails = Booking::VehicleDetail($booking);
            $booking->vehicle_number_plate_image = $booking->DriverVehicle ? get_image($booking->DriverVehicle->vehicle_number_plate_image, 'vehicle_document', $merchant_id, true, false) : "";
            $ployline = $booking->ploy_points;
            $booking_status = $booking->booking_status;
            $booking->share_able_link = "";
            $booking->otp_enable = $config->ride_otp;
            $booking->driver->fullName = $booking->driver->first_name . " " . $booking->driver->last_name;
            $location_editable = false;
            $marker_type = "PICK";
            $marker_lat = $booking->pickup_latitude;
            $marker_long = $booking->pickup_longitude;
            $tip_status = false;
            $string_file = $this->getStringFile($merchant_id);
            $BookingData = new BookingDataController();
            $dropLocation = $BookingData->NextLocation($booking->waypoints, $string_file);
            if (!empty($dropLocation) && $dropLocation['last_location'] == 1) {
                $drop_latitude = $dropLocation['drop_latitude'];
                $drop_longitude = $dropLocation['drop_longitude'];
                $drop_location = $dropLocation['drop_location'];
            } else {
                $drop_latitude = $booking->drop_latitude;
                $drop_longitude = $booking->drop_longitude;
                $drop_location = $booking->drop_location;
            }
            $sos_visibility = false;
            $shareable = false;
            switch ($booking_status) {
                case "1001":
                    $trip_status_text = trans("$string_file.accept_ride");
//                    $trip_status_text = $BookingData->LanguageData($merchant_id, 1);
                    $location = $booking->pickup_location;
                    $booking->cancelable = true;
                    $location_color = "2ecc71";
                    $location_headline = trans("$string_file.pickup");
//                    $location_headline = $BookingData->LanguageData($merchant_id, 6);
                    break;
                case "1002":
                    $trip_status_text = trans("$string_file.arriving");
//                        $BookingData->LanguageData($merchant_id, 14);
//                        trans("$string_file.arriving");
                    $location = $booking->pickup_location;
                    $booking->cancelable = true;
                    $location_color = "2ecc71";
                    $location_headline = trans("$string_file.pickup");
//                    $location_headline = $BookingData->LanguageData($merchant_id, 6);
                    break;
                case "1003":
                    $tip_status = ($app_config->tip_status == 1) ? true : false;
                    $tip_already_paid = $show_status;
                    $location_editable = ($booking->service_type_id == 1 && $booking->total_drop_location <= 1) ? true : false;
//                    $trip_status_text = trans("$string_file.driver_waiting_at_pickup");
//                        $BookingData->LanguageData($merchant_id, 15);
//                        trans("$string_file.driver_waiting_at_pickup");
                    $trip_status_text = trans("$string_file.driver_waiting_at_pickup");
//                    $trip_status_text = $BookingData->LanguageData($merchant_id, 15);
                    $location = $drop_location;
//                    $booking->cancelable = true;
                    $booking->cancelable = false;
                    $location_color = "e74c3c";
                    $location_headline = trans("$string_file.drop");
//                    $location_headline = $BookingData->LanguageData($merchant_id, 7);
                    $sos_visibility = $app_config->sos_user_driver == 1 ? true : false;
                    $marker_type = "DROP";
                    $marker_lat = $drop_latitude;
                    $marker_long = $drop_longitude;
                    break;
                case "1004":
                    $tip_status = ($app_config->tip_status == 1) ? true : false;

                    $trip_status_text = trans("$string_file.ride_started");
//                    $trip_status_text = $BookingData->LanguageData($merchant_id, 16);
                    $location = $drop_location;
                    $booking->cancelable = false;
                    $location_color = "e74c3c";
                    $location_headline = trans("$string_file.drop");
//                    $location_headline = $BookingData->LanguageData($merchant_id, 7);
                    $shareable = $booking->Segment->slag == 'TAXI' ? true : false;
                    $booking->share_able_link = $booking->unique_id ? route('ride.share', $booking->unique_id) : "";
                    $sos_visibility = $app_config->sos_user_driver == 1 ? true : false;
                    $marker_type = trans("$string_file.drop");
//                    $marker_type = $BookingData->LanguageData($merchant_id, 7);
                    $marker_lat = $drop_latitude;
                    $marker_long = $drop_longitude;
                    break;
                case "1005":
                    $trip_status_text = trans("$string_file.ride_completed");
//                    $trip_status_text = $BookingData->LanguageData($merchant_id, 17);
                    $location = $drop_location;
                    $booking->cancelable = false;
                    $location_color = "e74c3c";
                    $location_headline = trans("$string_file.drop");
//                    $location_headline = $BookingData->LanguageData($merchant_id, 7);
                    $marker_type = "DROP";
                    $marker_lat = $drop_latitude;
                    $marker_long = $drop_longitude;
                    break;
                case "1006":
                    $trip_status_text = trans("$string_file.user_cancel");
//                    $trip_status_text = $BookingData->LanguageData($merchant_id, 2);
                    $location = $booking->drop_location;
                    $location_color = "e74c3c";
                    $location_headline = "Drop";
                    break;
                case "1007":
                    $trip_status_text = trans("$string_file.driver_cancel");
//                    $trip_status_text = $BookingData->LanguageData($merchant_id, 10);
                    $location = $booking->drop_location;
                    $location_color = "e74c3c";
                    $location_headline = "Drop";
                    break;
                case "1008":
                    $trip_status_text = trans("$string_file.admin_cancel");
//                    $trip_status_text = $BookingData->LanguageData($merchant_id, 11);
                    $location = $booking->drop_location;
                    $location_color = "e74c3c";
                    $location_headline = "Drop";
                    break;
            }
            if ($tip_status == true && $booking->BookingDetail->tip_amount > 0) {
                $tip_status = false;
            }
            $booking->sos_visibility = $sos_visibility;
            $booking->shareable = $shareable;
            $booking->tip_status = $tip_status;
            $booking->tip_already_paid = $show_status;
            $location_object = array('trip_status_text' => $trip_status_text, 'location_headline' => $location_headline, 'location_text' => $location, 'location_color' => $location_color, 'location_editable' => $location_editable);
            $booking->location = $location_object;
            $driver_marker_type = array('driver_marker_type' => "CAR_ONE", 'driver_marker_lat' => $booking->driver->current_latitude, "driver_marker_long" => $booking->driver->current_longitude, 'driver_marker_bearing' => $booking->driver->bearing);
            $marker = array('marker_type' => $marker_type, 'marker_lat' => $marker_lat, "marker_long" => $marker_long);
            $polydata = array('polyline_width' => '8', 'polyline_color' => "333333", 'polyline' => $ployline);
            $booking->payment_editable = $config->change_payment_method == 1 ? true : false;
            $booking->polydata = $polydata;
            $booking->still_marker = $marker;
            $booking->movable_marker = $driver_marker_type;
            $booking->address_changeable = true;
            $booking->vehicle_details = $vehicleDetails;
            $booking->waypoints = json_decode($booking->waypoints, true);

            $booking->sos = Sos::AllSosList($merchant_id, 1, $booking->user_id);
            if ($merchant->Configuration->without_country_code_sos == 1) {
                $phoneCode = $booking->CountryArea->Country->phonecode;
                $booking->driver->phoneNumber = str_replace($phoneCode, '', $booking->driver->phoneNumber);
                $booking->User->UserPhone = str_replace($phoneCode, '', $booking->User->UserPhone);
            }
            $vehicle_color = $booking->DriverVehicle->vehicle_color;

            unset($booking->driver);
            $booking->vehicle_color = $vehicle_color;
            //overwriting vehicle type image
            $booking->VehicleType->vehicleTypeImage = get_image($booking->VehicleType->vehicleTypeImage, 'vehicle', $merchant_id, true, false);
            // driver profile image
            $booking->Driver->profile_image = get_image($booking->Driver->profile_image, 'driver', $merchant_id, true, false);

            $booing_config = BookingConfiguration::where('merchant_id', $booking->merchant_id)->first();
            $delivery_drop_otp = 0;
            $delivery_drop_qr = 0;
            $delivery_otp = '';
            if ($booking->Segment->slag == "DELIVERY" && isset($booing_config->delivery_drop_otp) && ($booing_config->delivery_drop_otp == 1 || $booing_config->delivery_drop_otp == 2)) {
                $delivery_details = BookingDeliveryDetails::where('booking_id', $booking->id)->orderBy('stop_no')->get();
                if (count($delivery_details) > 0) {
                    if ($booing_config->delivery_drop_otp == 1) {
                        $delivery_drop_otp = 1;
                    }
                    if ($booing_config->delivery_drop_otp == 2) {
                        $delivery_drop_qr = 1;
                    }
                    foreach ($delivery_details as $delivery_detail) {
                        if ($delivery_detail->drop_status == 0 && $delivery_detail->otp_status == 0 && $delivery_detail->stop_no != 0) {
                            if ($delivery_drop_otp) {
                                $delivery_otp = $delivery_detail->opt_for_verify;
                            }
                            if ($delivery_drop_qr) {
                                // creating qr code for scan with booking id stop id and otp
                                $delivery_otp = $booking->id . '_' . $delivery_detail->id . '_' . $delivery_detail->opt_for_verify;
                            }
                            break;
                        }
                    }
                    if ($delivery_otp == '') {
                        if ($delivery_drop_otp) {
                            $delivery_otp = $delivery_details[0]->opt_for_verify;
                        }
                        if ($delivery_drop_qr) {
                            $delivery_otp = $booking->id . '_' . $delivery_details[0]->id . '_' . $delivery_details[0]->opt_for_verify;
                        }
                    }
                }
            }
            // we are merging delivery and taxi otp so in case of delivery drop otp will be carried in ride_otp column of booking
            if ($booking->booking_status == 1004) {
                $booking->otp_enable = $delivery_drop_otp;
                // If qr code enable for delivery
                $booking->qr_enable = $delivery_drop_qr;
                $booking->ride_otp = $delivery_otp;
//             else
//             {
//                 $booking->ride_otp = "";
//             }
            }
            $otp = $booking->ride_otp;
            $booking->ride_otp = "$otp";
//            $booking->delivery_drop_otp = $delivery_drop_otp;
//            $booking->delivery_otp = $delivery_otp;
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage());
        }

        return $this->successResponse(trans("$string_file.ride"), $booking);
    }

    public function getFavouriteDrivers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checkout' => 'required',
//            'checkout_id' => ['required_if:checkout,1',
//                Rule::exists('booking_checkouts', 'id')->where(function ($query) {
//                    $query->where([['payment_method_id', '!=', 0]]);
//                })],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $checkout = '';
        $checkout_status = $request->checkout;
        $user = $request->user('api');
        $user_id = $user->id;
        if ($checkout_status == 1) {
            $checkout = BookingCheckout::find($request->checkout_id);
        }
        $string_file = $this->getStringFile(NULL, $user->Merchant);
        $drivers = FavouriteDriver::whereHas('Driver', function ($q) use ($checkout, $checkout_status) {
            if ($checkout_status == 1) {
                $q->whereHas('DriverVehicle', function ($q) use ($checkout) {
                    $q->whereHas('ServiceTypes', function ($q) use ($checkout) {
                        $q->where('service_type_id', $checkout->service_type_id);
                    });
                    $q->where('vehicle_type_id', $checkout->vehicle_type_id);
                    $q->where('vehicle_active_status', 1);
                });
                $q->where(['online_offline' => 1, 'login_logout' => 1, 'free_busy' => 2, 'country_area_id' => $checkout->country_area_id]);
            }
            $q->where(function ($qq) {
                $qq->where('driver_delete', '=', NULL);
                $qq->where('driver_admin_status', '=', 1);
            });
        })
            ->where([['user_id', '=', $user_id]])->get();
        if (empty($drivers) || $drivers->count() == 0) {
            return $this->failedResponse(trans("$string_file.no_favourite_driverss"));
        }
        foreach ($drivers as $value) {
            $value->fullName = $value->Driver->fullName;
            $value->phoneNumber = $value->Driver->phoneNumber;
            $value->profile_image = get_image($value->Driver->profile_image, 'driver', $value->Driver->merchant_id, true, false);
            $value->rating = $value->Driver->rating;
            $value->online_offline = $value->Driver->online_offline;
            $value->free_busy = $value->Driver->free_busy;
            if ($checkout_status == 1) {
                $value->vehicle_type = $value->Driver->DriverVehicle[0]->VehicleType->LanguageVehicleTypeSingle->vehicleTypeName;
                $value->base_fare = 10;
            }
        }
        return response()->json(['result' => "1", 'message' => trans("$string_file.ready_for_ride"), 'data' => $drivers]);
    }

    public function confirmBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'segment_id' => 'required|exists:segments,id',
            'checkout' => ['required', 'integer',
                Rule::exists('booking_checkouts', 'id')->where(function ($query) use ($request) {
                    $query->where([['segment_id', '=', $request->segment_id]]);
                    $query->where([['payment_method_id', '!=', 0]]);
                })],
            'question_id' => 'nullable|exists:questions,id',
            'fav_driver_id' => 'nullable|exists:drivers,id',
            'answer' => 'required_with:question_id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $checkout = BookingCheckout::find($request->checkout);
            $checkout->additional_notes = $request->additional_notes;
            $checkout->save();
            $string_file = $this->getStringFile($checkout->merchant_id);
            // booking data object
            $bookingData = new BookingDataController();
            if (isset($request->family_member_id)) {
                $checkout->family_member_id = $request->family_member_id;
                $checkout->save();
            }

            if ($this->CheckWalletBalance($checkout) == 1) {
                // check transaction table
                if (!empty($checkout->card_id)) {
                    $transaction = DB::table("transactions")->select("id", "booking_id", "reference_id", "checkout_id")
                        ->where([["reference_id", "=", $checkout->user_id], ["card_id", "=", $checkout->card_id], ["checkout_id", "=", $checkout->id], ["merchant_id", "=", $checkout->merchant_id], ["status", "=", 1]])->first();
                    if (empty($transaction)) {
                        $request->request->add(['card_id' => $checkout->card_id]);
                        $this->checkPaymentGatewayStatus($request, $checkout, $string_file);
                    }
                }

                if (!empty($request->fav_driver_id)) {
                    $fav_driver = Driver::where('id', '=', $request->fav_driver_id)->where('online_offline',1)->where('driver_delete',NULL)->first();
                    if (empty($fav_driver) || $fav_driver->free_busy == 1) {
                        throw new Exception(trans("$string_file.no_driver_available"));
                    } else {
                        $fav_driver->driver_id = $request->fav_driver_id;
                        $drivers = array($fav_driver);
                        $Bookingdata = $checkout->toArray();
                        unset($Bookingdata['id']);
                        unset($Bookingdata['user']);
                        unset($Bookingdata['created_at']);
                        unset($Bookingdata['updated_at']);
                        $Bookingdata['booking_timestamp'] = time();
                        $Bookingdata['booking_status'] = 1001;
                        $booking = Booking::create($Bookingdata);
                        $newdriver = new FindDriverController();
                        $newdriver->AssignRequest($drivers, $booking->id);
                        $message = trans("$string_file.new_ride");
//                        $message = $bookingData->LanguageData($booking->merchant_id, 25);
                        $bookingData->SendNotificationToDrivers($booking, $drivers, $message);

                        $arr_return = [
                            'message' => trans("$string_file.ride_booked"),
                            'data' => $booking
                        ];
                        $booking = $arr_return;
                        //return ['result' => "1", 'message' => trans("$string_file.ride_booked"), 'data' => $booking];
                    }
                } else {
                    if (!empty($request->question_id) && !empty($request->answer)) {
                        $QuestionUser = QuestionUser::where([['question_id', '=', $request->question_id], ['answer', '=', $request->answer], ['user_id', '=', $request->user('api')->id]])->first();
                        if (empty($QuestionUser)) {
//                            throw new Exception(trans('api.answerwrong'));
                        }
                    }
                    switch ($checkout->ServiceType->type) {
                        case "1":
                            $newBooking = new NormalController();
                            if ($checkout->booking_type == 1) {
                                $booking = $newBooking->currentBookingAssign($checkout);
                            } else {
                                $booking = $newBooking->laterBookingAssign($checkout);
                            }
                            break;
                        case "2":
                            $newBooking = new RentalController();
                            if ($checkout->booking_type == 1) {
                                $booking = $newBooking->currentBookingAssign($checkout);
                            } else {
                                $booking = $newBooking->laterBookingAssign($checkout);
                            }
                            break;
//                    case "3":
//                        $newBooking = new TransferController();
//                        if ($checkout->booking_type == 1) {
//                            $booking = $newBooking->CurrentBookingAssign($checkout);
//                        } else {
//                            $booking = $newBooking->LeterBookingAssign($checkout);
//                        }
//                        break;
                        case "4":
                            $newBooking = new OutstationController();
                            if ($checkout->booking_type == 1) {
                                $booking = $newBooking->currentBookingAssign($checkout);
                            } else {
                                $booking = $newBooking->bookingAssign($checkout);
                            }
                            break;
                        case "5":
                            $newBooking = new PoolController();
                            $booking = $newBooking->Booking($checkout);
                    }
                    $merchant_id = $request->user('api')->merchant_id;
                    $SmsConfiguration = SmsConfiguration::select('ride_book_enable', 'ride_book_msg')->where([['merchant_id', '=', $merchant_id]])->first();
                    if (!empty($SmsConfiguration) && $SmsConfiguration->ride_book_enable == 4 && $SmsConfiguration->ride_book_msg) {
                        $sms = new SmsController();
                        $phone = $request->user('api')->UserPhone;
                        $sms->SendSms($merchant_id, $phone, null, 'RIDE_BOOK', $request->user('api')->email);
                    }
                }
            } elseif ($this->CheckWalletBalance($checkout) == 2) {
                return $this->failedResponse(trans("$string_file.low_wallet_warning"));
            } else {
                return $this->failedResponse(trans("$string_file.wallet_low_estimate"));
            }

            $data = $booking['data'];
            // update transaction table
            if (!empty($data->card_id)) {
                $transaction = DB::table("transactions")->select("id", "booking_id", "reference_id", "checkout_id")
                    ->where([["reference_id", "=", $data->user_id], ["card_id", "=", $data->card_id], ["checkout_id", "=", $checkout->id], ["merchant_id", "=", $data->merchant_id], ["status", "=", 1]])->first();
                if (!empty($transaction->id)) {
                    DB::table("transactions")->where("id", $transaction->id)->update(["booking_id" => $data->id]);
                } else {
                    $newArray = BookingCheckout::find($request->checkout);
                    $this->checkPaymentGatewayStatus($request, $newArray, $string_file);
                }
            }
//            will delete checkout in accept api because we have to give retry button if driver don't accept ride
           // $checkout->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        $message = $booking['message'];
        $return_data = [
            'id' => $data->id,
            'booking_type' => $data->booking_type,
            'merchant_booking_id' => $data->merchant_booking_id,
            'ride_radius_increase_api_call_time' => 10
        ];

        // inset booking status history
        $this->saveBookingStatusHistory($request, $data, $data->id);

        // Send new ride request mail to merchant
        //$bookingData->sendBookingMail($data);

        return $this->successResponse($message, $return_data);
    }

    public function CheckWalletBalance($data)
    {
        $merchantData = PriceCard::select('minimum_wallet_amount')->find($data->price_card_id);
        $set_amount = empty($merchantData['minimum_wallet_amount']) ? 0 : $merchantData['minimum_wallet_amount'];
        $user_amount = empty($data->User->wallet_balance) ? 0 : $data->User->wallet_balance;
        $config = Configuration::select('user_wallet_status')->where('merchant_id', '=', $data->merchant_id)->first();
        $merchantConfig = Merchant::select('cancel_amount_deduct_from_wallet')->find($data->merchant_id);
        if ($data) {
            switch ($data->payment_method_id) {
                case '3':
                    if ($user_amount >= $data->estimate_bill) {
                        return 1;
                    } else {
                        return 0;
                    }
                    break;
                case '1':
                    if ($config->user_wallet_status == 1 && $merchantConfig->cancel_amount_deduct_from_wallet == 1) {
                        if ($user_amount >= $set_amount) {
                            return 1;
                        } else {
                            return 2;
                        }
                        break;
                    } else {
                        return 1;
                    }
                default :
                    return 1;
                    break;
            }
        }
    }

    public function checkPaymentGatewayStatus($request, $newArray,$string_file ="")
    {
        try {
            $card = UserCard::select('id', 'payment_option_id', 'card_number', 'token')->find($request->card_id);
            $payment_option = PaymentOptionsConfiguration::where([['payment_option_id', '=', $card->payment_option_id], ['merchant_id', '=', $newArray->merchant_id]])->first();
            if ($payment_option->PaymentOption->slug == "PAYU" && $payment_option->payment_step > 1) {
                $user = $request->user('api');
                $locale = $request->header('locale');
                $amount = $newArray->estimate_bill;
                // initiate payment card authorise
                $payment = new RandomPaymentController;
                $payment_data = $payment->payuPaymentAuthorization($user, $amount, $card, $payment_option, $locale);
                if(isset($payment_data['code']) && $payment_data['code'] == "SUCCESS"&& $payment_data['transactionResponse']['state'] == "APPROVED")
                {
                    // entry in transactions table
                    DB::table('transactions')->insert([
                        'status' => 1, // for user
                        'reference_id' => $user->id,
                        'card_id' => $card->id,
                        'merchant_id' => $newArray->merchant_id,
                        'payment_option_id' => $card->payment_option_id,
                        'checkout_id' => $newArray->id,
                        'payment_transaction' => json_encode($payment_data),
                    ]);
                }
                elseif(isset($payment_data['code']) && $payment_data['code'] == "SUCCESS" && $payment_data['transactionResponse']['state'] == "DECLINED")
                {
                    // $payment->payuPaymentVoid($booking->User, $amount, $booking->UserCard,$payment_config,$locale,$transaction);
                    $message = isset($payment_data['transactionResponse']['paymentNetworkResponseErrorMessage']) ? $payment_data['transactionResponse']['paymentNetworkResponseErrorMessage'] : $payment_data['transactionResponse']['responseCode'];
                    throw new Exception(trans("$string_file.payment_failed") . ' : ' . $message);

                }
                else
                {
                    $message = isset($payment_data['error']) ? $payment_data['error'] : "";
                    throw new Exception(trans("$string_file.payment_failed") . ' : ' . $message);
                }

            }
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // its uses when payment method is selecting while checkout
    public function checkoutPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checkout' => 'required|integer|exists:booking_checkouts,id',
            'payment_option' => 'required|integer|exists:payment_methods,id',
//            'payment_option_id' => 'required|integer|exists:payment_options,id',
            'card_id' => 'required_if:payment_option,2',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
            // return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        DB::beginTransaction();
        try {

            $string_file = $this->getStringFile($request->merchant_id);
            // check user number is payment option is payphone
            if(!empty($request->payment_option_id) && $request->payment_option == 4)
            {
                $option = PaymentOption::select('slug','id')->where('id',$request->payment_option_id)->first();
                if(!empty($option) && $option->slug == 'PAYPHONE')
                {
                     $payment_option_config  = $option->PaymentOptionConfiguration;
                     $payphone =  new PayPhoneController;
                     $payphone->validateUser($request,$payment_option_config,$string_file);
                }
            }
            $newArray = BookingCheckout::find($request->checkout);
            // check wallet balance with estimate
            $estimate_amount = $newArray->estimate_bill;
            if ($request->payment_option == 3 && ($estimate_amount > $newArray->User->wallet_balance)) {
                $message = trans_choice("$string_file.low_wallet_warning", 3, ['AMOUNT' => $estimate_amount]);
                return $this->failedResponse($message);
            }

            // check card status on payu payment
            if (!empty($request->card_id)) {
                $this->checkPaymentGatewayStatus($request, $newArray);
            }
            
            // check card has enough balance for ride or not
            $newArray->payment_method_id = $request->payment_option;
            if (!empty($request->card_id)) {
                $card = new CardController();
                $response = $card->checkUserCardBalance($request->card_id, ($newArray->estimate_bill ?? 0) * 2);
                if (!$response['status']) {
                    return $this->failedResponse($response['message']);
                }
            }
            $newArray->card_id = !empty($request->card_id) ? $request->card_id : NULL;
            $newArray->save();
            $bookingData = new BookingDataController();
//            if ($newArray->delivery_type_id) {
//                $result = new DeliveryCheckoutResource($newArray);
////                $result = $bookingData->DeliveryCheckOut($newArray);
//            } else {
            $result = $bookingData->CheckOut($newArray);
//            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();

        return $this->successResponse(trans("$string_file.ready_for_ride"), $result);
        // return response()->json(['result' => "1", 'message' => trans("$string_file.ready_for_ride"), 'data' => $result]);
    }
    // to book user ride with current and drop location
    // book now and book later now : 1, later : 2
    public function Checkout(Request $request)
    {
        $log_data = array(
            'request_type' => 'Checkout Request',
            'data' => $request->all(),
            'additional_notes' => 'checkout api'
        );
        booking_log($log_data);
        $user = $request->user('api');

        $config = Configuration::where('merchant_id', $user->merchant_id)->first();
        if (isset($config->user_outstanding_enable) && $config->user_outstanding_enable == 1) {
            // Check for previous booking outstanding.
            $result = $this->checkBookingOutstanding($request->user('api')->id);
            if (!empty($result)) {
                return response()->json(['result' => "3", 'message' => 'success', 'data' => $result]);
            }
        }

        $validator = Validator::make($request->all(), [
            'segment_id' => 'required|integer|exists:segments,id',
            'area' => 'required|integer|exists:country_areas,id',
            'service_type' => 'required|integer|exists:service_types,id',
            'vehicle_type' => 'required_if:service_type,1|required_if:service_type,2,required_if:service_type,3|required_if:service_type,4',
            'pickup_latitude' => 'required',
            'pickup_longitude' => 'required',
            'pick_up_locaion' => 'required',
            'booking_type' => 'required|integer|in:1,2',
            'service_package_id' => 'required_if:service_type,2|required_if:service_type,3',
            'later_date' => 'required_if:booking_type,2',
            'later_time' => 'required_if:booking_type,2',
            'total_drop_location' => 'required|integer|between:0,4',
            'drop_location' => 'required_if:total_drop_location,1,2,3,4',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $service = ServiceType::select('id', 'type')->Find($request->service_type);
            switch ($service->type) {
                case "1":
                    $normalBooking = new NormalController();
                    if ($request->booking_type == 1) {
                        $booking = $normalBooking->CurrentBookingCheckout($request);
                    } else {
                        $booking = $normalBooking->LaterBookingCheckout($request);
                    }
                    break;
                case "2":
                    $rentalBooking = new RentalController();
                    if ($request->booking_type == 1) {
                        $booking = $rentalBooking->CurrentBookingCheckout($request);
                    } else {
                        $booking = $rentalBooking->LaterBookingCheckout($request);
                    }
                    break;
                case "3":
                    $transferBooking = new TransferController();
                    if ($request->booking_type == 1) {
                        $booking = $transferBooking->CurrentBookingCheckout($request);
                    } else {
                        $booking = $transferBooking->LaterBookingCheckout($request);
                    }
                    break;
                case "4":
                    $outstation = new OutstationController();
                    $booking = $outstation->CheckOut($request);
                    break;
                case "5":
                    $newBooking = new PoolController();
                    $booking = $newBooking->CreateCheckout($request);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();

        $log_data = array(
            'request_type' => 'Checkout Response',
            'data' => $booking,
            'additional_notes' => 'checkout api'
        );
        booking_log($log_data);
        return $this->successResponse($booking['message'], $booking['data']);
    }

    public function userTracking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|integer|exists:bookings,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $booking = Booking::select('id', 'vehicle_type_id', 'total_drop_location', 'service_type_id', 'driver_id', 'merchant_id', 'booking_status', 'pickup_location', 'pickup_latitude', 'pickup_longitude', 'waypoints', 'drop_latitude', 'drop_longitude', 'drop_location', 'country_area_id', 'ploy_points')
                ->with(['Driver' => function ($query) {
                    $query->select('id', 'email', 'phoneNumber', 'profile_image', 'rating', 'current_latitude', 'current_longitude', 'bearing');
                }])
                ->find($request->booking_id);
            // $booking->Driver->fullName = $booking->Driver->fullName;
            $merchant = Merchant::select('id')->find($booking->merchant_id);
            $string_file = $this->getStringFile(NULL, $booking->Merchant);
            $app_config = $merchant->ApplicationConfiguration;
            $booking_status = $booking->booking_status;
            $marker_type = "PICK";
            $marker_lat = $booking->pickup_latitude;
            $marker_long = $booking->pickup_longitude;
            $location_editable = false;
            $tip_status = false;
            $bookingData = new BookingDataController();
            $dropLocation = $bookingData->NextLocation($booking->waypoints, $string_file);
            if (!empty($dropLocation) && $dropLocation['last_location'] == 1) {
                $drop_latitude = $dropLocation['drop_latitude'];
                $drop_longitude = $dropLocation['drop_longitude'];
                $drop_location = $dropLocation['drop_location'];
            } else {
                $drop_latitude = $booking->drop_latitude;
                $drop_longitude = $booking->drop_longitude;
                $drop_location = $booking->drop_location;
            }
            $est_driver_time = "";
            $est_driver_distance = "";
            $poly_line = $booking->ploy_points;
            switch ($booking_status) {
                case "1001":
                    $trip_status_text = trans("$string_file.accept_ride");
//                    $trip_status_text = $bookingData->LanguageData($booking->merchant_id, 1);
                    $location = $booking->pickup_location;
                    $location_color = "2ecc71";
                    $location_headline = trans("$string_file.pickup");
//                    $location_headline = $bookingData->LanguageData($booking->merchant_id, 6);
                    $cancelable = true;
                    break;
                case "1002":
                    $trip_status_text = trans("$string_file.arrived_pickup");
//                    $trip_status_text = $bookingData->LanguageData($booking->merchant_id, 12);
                    $location = $booking->pickup_location;
                    $location_color = "2ecc71";
                    $location_headline = trans("$string_file.pickup");
//                    $location_headline = $bookingData->LanguageData($booking->merchant_id, 6);
                    $cancelable = true;
                    if ($merchant->BookingConfiguration->driver_eta_est_distance == 1) {
                        $eta = $this->EtaCalculation($booking);
                        $est_driver_time = $eta['time'];
                        $est_driver_distance = $eta['distance'];
                        $poly_line = $eta['poly_point'];
                    }
                    $marker_lat = $booking->pickup_latitude;
                    $marker_long = $booking->pickup_longitude;
                    break;
                case "1003":
                    $tip_status = ($app_config->tip_status == 1) ? true : false;
                    $trip_status_text = trans("$string_file.started_from_pickup");
//                    $trip_status_text = $bookingData->LanguageData($booking->merchant_id, 13);
                    $location = $drop_location;
                    $location_editable = ($booking->service_type_id == 1 && $booking->total_drop_location <= 1) ? true : false;
                    $location_color = "e74c3c";
                    $location_headline = trans("$string_file.drop");
//                    $location_headline = $bookingData->LanguageData($booking->merchant_id, 7);
                    $marker_type = "Drop";
                    $marker_lat = $drop_latitude;
                    $marker_long = $drop_longitude;
                    $cancelable = false;
                    break;
                case "1004":
                    $tip_status = ($app_config->tip_status == 1) ? true : false;
                    $trip_status_text = trans("$string_file.ride_completed");
//                    $trip_status_text = $bookingData->LanguageData($booking->merchant_id, 9);
                    $location_editable = ($booking->service_type_id == 1 && $booking->total_drop_location <= 1) ? true : false;
                    $location = $drop_location;
                    $location_color = "e74c3c";
                    $location_headline = trans("$string_file.drop");
//                    $location_headline = $bookingData->LanguageData($booking->merchant_id, 7);
                    $cancelable = false;
                    $marker_type = "DROP";
                    $marker_lat = $drop_latitude;
                    $marker_long = $drop_longitude;
                    break;
                case "1005":
                    $trip_status_text = trans("$string_file.ride_completed");
//                    $trip_status_text = $bookingData->LanguageData($booking->merchant_id, 9);
                    $location = $drop_location;
                    $location_color = "e74c3c";
                    $location_headline = trans("$string_file.drop");
//                    $location_headline = $bookingData->LanguageData($booking->merchant_id, 7);
                    $cancelable = false;
                    $marker_type = "DROP";
                    $marker_lat = $drop_latitude;
                    $marker_long = $drop_longitude;
                    break;
                case "1006":
                    $trip_status_text = trans("$string_file.user_cancel");
//                    $trip_status_text = $bookingData->LanguageData($booking->merchant_id, 2);
                    $location = $booking->drop_location;
                    $location_color = "e74c3c";
                    $location_headline = trans("$string_file.drop");
//                    $location_headline = $bookingData->LanguageData($booking->merchant_id, 7);
                    $cancelable = false;
                    break;
                case "1007":
                    $trip_status_text = trans("$string_file.driver_cancel");
//                    $trip_status_text = $bookingData->LanguageData($booking->merchant_id, 10);
                    $location = $booking->drop_location;
                    $location_color = "e74c3c";
                    $location_headline = trans("$string_file.drop");
//                    $location_headline = $bookingData->LanguageData($booking->merchant_id, 7);
                    $cancelable = false;
                    break;
                case "1008":
                    $trip_status_text = trans("$string_file.admin_cancel");
//                    $trip_status_text = $bookingData->LanguageData($booking->merchant_id, 11);
                    $location = $booking->drop_location;
                    $location_color = "e74c3c";
                    $location_headline = trans("$string_file.drop");
//                    $location_headline = $bookingData->LanguageData($booking->merchant_id, 7);
                    $cancelable = false;
                    break;
            }

            $location_object = array('estimate_driver_time' => $est_driver_time, 'estimate_driver_distnace' => $est_driver_distance, 'trip_status_text' => $trip_status_text, 'location_headline' => $location_headline, 'location_text' => $location, 'location_color' => $location_color, 'location_editable' => $location_editable);
            // Please don't remove commented code. its for transi
            //        $eta_call = false;
            //        if($eta_call == true)
            //        {
            //            $eta = $this->EtaCalculation($booking);
            //            $location_object = array('estimate_driver_time' => $eta['time'], 'estimate_driver_distnace' => $eta['distance'], 'trip_status_text' => $trip_status_text, 'location_headline' => $location_headline, 'location_text' => $location, 'location_color' => $location_color, 'location_editable' => $location_editable);
            //        }
            $newArray = array();
            // live tacking notice at driver app and then save into db, user location is same as drive current location
            $driver_marker_type = array('driver_marker_name' => explode_image_path($booking->VehicleType->vehicleTypeMapImage), 'driver_marker_type' => "CAR_ONE", 'driver_marker_lat' => $booking->Driver->current_latitude, "driver_marker_long" => $booking->Driver->current_longitude, 'driver_marker_bearing' => $booking->Driver->bearing, 'driver_marker_accuracy' => $booking->Driver->accuracy);
            $marker = array('marker_type' => $marker_type, 'marker_lat' => $marker_lat, "marker_long" => $marker_long);
            $polydata = array('polyline_width' => '8', 'polyline_color' => "333333", 'polyline' => $poly_line);
            $newArray['stil_marker'] = $marker;
            $newArray['tip_status'] = $tip_status;
            $newArray['movable_marker_type'] = $driver_marker_type;
            $newArray['polydata'] = $polydata;
            $newArray['location'] = $location_object;
            $newArray['cancelable'] = $cancelable;
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse(trans("$string_file.data_found"), $newArray);
    }

    public function EtaCalculation(Booking $booking)
    {
        $eta = array('time' => '', 'distance' => '', 'poly_point' => '');
        if ($booking->booking_status == 1002):
            $configuration = BookingConfiguration::select('google_key', 'driver_eta_est_distance', 'polyline')->where([['merchant_id', '=', $booking->merchant_id]])->first();
            if ($configuration->driver_eta_est_distance == 1):
                $driver_current_location = $booking->driver->current_latitude . ',' . $booking->driver->current_longitude;
                $pick_lat_long = $booking->pickup_latitude . ',' . $booking->pickup_longitude;
                $units = ($booking->CountryArea->Country['distance_unit'] == 1) ? 'metric' : 'imperial';
                $poly_line = $configuration->polyline == 1 ? true : false;
                $eta = GoogleController::GoogleDistanceAndTime($driver_current_location, $pick_lat_long, $configuration->google_key, $units, $poly_line);
            endif;
        endif;
        return $eta;
    }


    // cancel booking by driver
    public function cancelBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'cancel_reason_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new Exception($errors[0]);
        }
        DB::beginTransaction();
        try {
            $merchant_id = $request->user('api-driver')->merchant_id;
            $driver = $request->user('api-driver');
            $booking_id = $request->id;
            $booking = Booking::find($booking_id);
            $string_file = $this->getStringFile($booking->Merchant);
            if ($booking->booking_status == '1012') {
                $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
                switch ($booking->service_type_id) {
                    case "1":
                        $config = $configuration->normal_ride_later_request_type;
                        $ride_later_radius = $configuration->normal_ride_later_radius;
                        $ride_later_request_driver = $configuration->normal_ride_later_request_driver;
                        break;
                    case "2":
                        $config = $configuration->rental_ride_later_request_type;
                        $ride_later_radius = $configuration->rental_ride_later_radius;
                        $ride_later_request_driver = $configuration->rental_ride_later_request_driver;
                        break;
                    case "3":
                        $config = $configuration->transfer_ride_later_request_type;
                        $ride_later_radius = $configuration->transfer_ride_later_radius;
                        $ride_later_request_driver = $configuration->transfer_ride_later_request_driver;
                        break;
                    case "4":
                        $config = $configuration->outstation_ride_later_request_type;
                        $ride_later_radius = $configuration->outstation_ride_now_radius;
                        $ride_later_request_driver = $configuration->outstaion_ride_now_request_driver;
                        break;
                }
                if ($config == 1) {
                    DriverCancelBooking::create([
                        'booking_id' => $request->booking_id,
                        'driver_id' => $request->user('api-driver')->id,
                    ]);
                    BookingRequestDriver::where([['driver_id', '=', $driver->id], ['booking_id', '=', $booking->id]])->update(['request_status' => 4]);
                    $booking->booking_status = 1001;
                    $booking->driver_id = NULL;
                    $booking->save();
                    $findDriver = new FindDriverController();
                    $user_gender = $booking->gender;
                    $drivers = $findDriver->GetAllNearestDriver($booking->country_area_id, $booking->pickup_latitude, $booking->pickup_longitude, $ride_later_radius, $ride_later_request_driver, $booking->vehicle_type_id, $booking->service_type_id, '', '', $user_gender, $configuration->driver_request_timeout);
                    if (!empty($drivers)) {
                        $message = trans("$string_file.new_upcoming_ride");
                        $bookingData = new BookingDataController();
                        $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
                    }
                }
                // charge driver wallet
                $today = new DateTime(date('Y-m-d H:i:s'));
                $today = $today->format("Y-m-d H:i:s");
                $ride_later_cancel_hour = $configuration->ride_later_cancel_hour ? $configuration->ride_later_cancel_hour : 0;
                $bookingtimestamp = $booking['later_booking_date'] . " " . $booking->later_booking_time;
                $DateTime = new DateTime($bookingtimestamp);
                $totmin = $ride_later_cancel_hour * 60;
                $min = $totmin % 60;
                $hour = explode('.', ($totmin / 60));
                if ($hour[0] != 0) {
                    $str = $min != 0 ? "-{$hour[0]} hours -{$min} minutes" : "-{$hour[0]} hours";
                } else {
                    $str = $min != 0 ? "-{$min} minutes" : '-0 minutes';
                }
                $DateTime->modify($str);
                $newDate = $DateTime->format("Y-m-d H:i:s");
                if ($newDate <= $today) {
                    $paramArray = array(
                        'driver_id' => $booking->driver_id,
                        'booking_id' => $booking->id,
                        'amount' => $configuration->ride_later_cancel_charge_in_cancel_hour,
                        'narration' => 8,
                    );
                    WalletTransaction::WalletDeduct($paramArray);
                    //                $current_driver = Driver::find($booking->driver_id);
//                    \App\Http\Controllers\Helper\CommonController::WalletDeduct($booking->driver_id,$booking->id,$configuration->ride_later_cancel_charge_in_cancel_hour,8);
//                $current_driver->wallet_money = $current_driver->wallet_money - $configuration->ride_later_cancel_charge_in_cancel_hour;
//                $current_driver->save();
//                WalletTransaction::driverWallet($current_driver, $configuration->ride_later_cancel_charge_in_cancel_hour, 2, $booking->id);
                }
            } else {
                $bookingConfig = $booking->Merchant->BookingConfiguration;
                $limit_minute = $bookingConfig->driver_cancel_after_time;
                if($bookingConfig->driver_cancel_ride_after_time == 1 && !empty($limit_minute)){
                    $bookingDetails = $booking->BookingDetail;
                    $current_cancel_datetime = date( 'Y-m-d H:i:s', strtotime( "now" ) );
                    $driver_arrive_datetime = date( 'Y-m-d H:i:s', strtotime( $bookingDetails->arrive_timestamp ));
                    $diff = date_diff( date_create($driver_arrive_datetime), date_create($current_cancel_datetime) );
                    $diff_minute = $diff->i;
                    if ($diff_minute >= $limit_minute){
                        $cancel_charges = $booking->PriceCard->cancel_amount;
                        $merchant_data = $booking->Merchant;
                        if ($merchant_data['cancel_outstanding'] == 1):
                            $merchant = new \App\Http\Controllers\Helper\Merchant();
                            $payment = new CancelPayment();
                            $cancel_charges_received = $payment->MakePayment($booking, $booking->payment_method_id, $cancel_charges, $booking->user_id, $booking->card_id, $merchant_data->cancel_outstanding, $booking->driver_id);
                            $booking_transaction_submit = BookingTransaction::updateOrCreate([
                                'booking_id' => $booking_id,
                            ], [
                                'date_time_details' => date('Y-m-d H:i:s'),
                                'sub_total_before_discount' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                                'surge_amount' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                                'extra_charges' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                                'discount_amount' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                                'tax_amount' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                                'tip' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                                'insurance_amount' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                                'cancellation_charge_received' => $merchant->TripCalculation(isset($cancel_charges_received) ? $cancel_charges_received : '0.0', $booking->merchant_id),
                                'cancellation_charge_applied' => $merchant->TripCalculation($cancel_charges, $booking->merchant_id),
                                'toll_amount' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                                'cash_payment' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                                'online_payment' => $merchant->TripCalculation(isset($cancel_charges_received) ? $cancel_charges_received : '0.0', $booking->merchant_id),
                                'customer_paid_amount' => $merchant->TripCalculation(isset($cancel_charges_received) ? $cancel_charges_received : '0.0', $booking->merchant_id),
                                'company_earning' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                                'driver_earning' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                                'amount_deducted_from_driver_wallet' => $merchant->TripCalculation('0.0', $booking->merchant_id),
                                'driver_total_payout_amount' => $merchant->TripCalculation($cancel_charges, $booking->merchant_id),
                                'trip_outstanding_amount' => $merchant->TripCalculation(($cancel_charges - (isset($cancel_charges_received) ? $cancel_charges_received : 0)), $booking->merchant_id),
//                            'merchant_id'=>$booking->merchant_id
                            ]);
                            if (!empty($booking->driver_id)) {
                                $paramArray = array(
                                    'merchant_id' => $booking->merchant_id,
                                    'driver_id' => $booking->driver_id,
                                    'booking_id' => $booking->id,
                                    'amount' => $cancel_charges,
                                    'narration' => 11,
                                    'platform' => 1,
                                    'payment_method' => 2,
                                );
                                WalletTransaction::WalletCredit($paramArray);
//                    \App\Http\Controllers\Helper\CommonController::WalletCredit($booking->driver_id,$booking->id,$cancel_charges,11,1,2);
                            }
                        endif;
                    }
                }

                $booking->booking_status = 1007;
                $booking->cancel_reason_id = $request->cancel_reason_id;
                $booking->save();
                // inset booking status history
                $this->saveBookingStatusHistory($request, $booking, $booking->id);

                //payment option is payu then void the authorisation
                if ($booking->payment_method_id == 2) {
                    $user_card = UserCard::find($booking->card_id);
                    if ($user_card->PaymentOption->slug == "PAYU") {
                        $locale = $request->header('locale');
                        $this->payuVoid($booking, $locale);
                    }
                }
                $request->user('api-driver')->free_busy = 2;
                $request->user('api-driver')->save();
                $bookingData = new BookingDataController();
                $bookingData->bookingNotificationForUser($booking, "CANCEL_RIDE");
            }
            if ($booking->service_type_id == 5 && !empty($booking->driver_id)) {
                $poolBooking = new PoolController();
                $poolBooking->CancelRide($booking, $request);
            }
            DriverRecords::penaltyDriver($driver->id);

            // clear the call masking session
            $config = Configuration::where('merchant_id',$booking->merchant_id)->first();
            if(isset($config->twilio_call_masking) && $config->twilio_call_masking == 1){
                TwilioMaskingHelper::close_session($booking);
            }

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        DB::commit();
        return ['message' => trans("$string_file.ride_cancelled"), 'data' => []];
    }

    // void request of payment for payu
    public function payuVoid($booking, $locale)
    {
        $user_card = UserCard::find($booking->card_id);
        $payment_config = PaymentOptionsConfiguration::where([['payment_option_id','=',$user_card->payment_option_id],['merchant_id','=',$booking->merchant_id]])->first();
        $payment = new RandomPaymentController();
        $transaction = [];
        if($payment_config->payment_step > 1)
        {
            $transaction = DB::table("transactions")->select("id","payment_transaction")->where([["reference_id","=",$booking->user_id],["card_id","=",$booking->card_id],["booking_id","=",$booking->id],["status","=",1]])->first();
            $transaction= !empty($transaction->payment_transaction)  ? json_decode($transaction->payment_transaction,true) : [];
        }
        $payment_data = $payment->payuPaymentVoid($booking->User, $booking->final_amount_paid, $booking->UserCard,$payment_config,$locale,$transaction);
    }



    // driver update payment status when user pay cash to driver and user phone goes to off/hang
    public function driverRidePaymentStatus(Request $request)
    {
        $status = 0;
        $string_file = $this->getStringFile($request->merchant_id);
        $message = trans("$string_file.data_not_found");
        $validator = Validator::make($request->all(), [
//            'booking_id' => 'required',
            'latitude' => 'required',
            'segment_slug' => 'required',
//            'accuracy' => 'required',
            'booking_order_id' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            $this->failedResponse($errors[0]);
//            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $merchant = new \App\Http\Controllers\Helper\Merchant();
        DB::beginTransaction();
        try {
            $booking = Booking::with('BookingDetail', 'User','Merchant')->where('payment_status', '!=', 1)->where('booking_status', 1005)->find($request->booking_order_id);
            if (isset($booking->id) && !empty($booking->id)) {
                $previous_payment_method = $booking->payment_method_id;
                $booking->payment_method_id = 1; // its case in which driver receive amount only in cash
                $booking->payment_status = 1; // means payment done successfully
                $booking->save();

                if (isset($booking->BookingDetail->id)) {
                    $booking->BookingDetail->pending_amount = 0;
                    $booking->BookingDetail->payment_failure = 2;
                    $booking->BookingDetail->save();
                }

                if (isset($booking->BookingTransaction->id) && $previous_payment_method != 1) {
                    $booking->BookingTransaction->cash_payment = $booking->BookingTransaction->online_payment;
                    $booking->BookingTransaction->online_payment = '0.0';
                    $booking->BookingTransaction->trip_outstanding_amount = $merchant->TripCalculation(($booking->BookingTransaction->driver_total_payout_amount + $booking->BookingTransaction->amount_deducted_from_driver_wallet - $booking->BookingTransaction->cash_payment), $booking->merchant_id);
                    $booking->BookingTransaction->instant_settlement = 0;
                    $booking->BookingTransaction->save();
                }
                $status = 1;
                $message = trans("$string_file.success");

                // send amount accepted by driver notification to user
                if ($status == 1) {
;
                    $bookingData = new BookingDataController();
                    // $message = $bookingData->LanguageData($booking->merchant_id, 31);
                    $notification_data = $bookingData->bookingNotificationForUser($booking, "END_RIDE");
                    event(new SendUserInvoiceMailEvent($booking, 'invoice'));
                }
            } else {
                // if user already paid through cash
                $booking = Booking::where('payment_status', '=', 1)->where('payment_method_id', '=', 1)->where('booking_status', 1005)->find($request->booking_order_id);
                if (isset($booking->id) && !empty($booking->id)) {
                    $status = 1;
                    $message = trans("$string_file.success");
                }
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            DB::rollback();
            return $this->failedResponse($message);
        }
        DB::commit();
        if ($status == 0) {
            return $this->failedResponse($message);
        } else {
            $booking_data = new BookingDataController;
            $request->request->add(['booking_id' => $request->booking_order_id]);
            $return_data = $booking_data->bookingReceiptForDriver($request);
            return $this->successResponse($message, $return_data);
        }
//        return response()->json(['result' => "$status", 'message' => $message]);
    }

    public function getNextRadiusDriver(Request $request)
    {
        $bookingData = new BookingDataController();
        $result = $bookingData->sendRequestToNextDrivers($request->booking_id, 1);
        return $result;
    }

    public function findDrivers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checkout' => ['required', 'integer',
                Rule::exists('booking_checkouts', 'id')->where(function ($query) {
                    $query->where([['payment_method_id', '!=', 0]]);
                })],
            'question_id' => 'nullable|exists:questions,id',
            'fav_driver_id' => 'nullable|exists:drivers,id',
            'answer' => 'required_with:question_id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $checkout = BookingCheckout::find($request->checkout);
        if ($this->CheckWalletBalance($checkout) == true) {

        }
    }

    public function RidePauseResume(Request $request)
    {
        $driver = $request->user('api-driver');
        $merchant_id = $driver->merchant_id;
        $validator = Validator::make($request->all(), [
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->where('booking_status', 1004);
                }),
            ],
            'latitude' => 'required',
            'longitude' => 'required',
            'type' => 'required' // 1 for pause, 2 for resume
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $booking_id = $request->booking_id;
        $booking = Booking::find($booking_id);
        $generalConfiguration = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        $date_time = date("Y-m-d H:i:s");
        $startTimeStemp = strtotime($date_time);
        $price_card = DB::table('price_card_values as pvc')->join('pricing_parameters as pp', 'pvc.pricing_parameter_id', '=', 'pp.id')->where([['pvc.price_card_id', '=', $booking->price_card_id], ['pp.parameterType', '=', 18]])->get();
        $type = 0;
        $message = trans('api.no_action');
        $bookingData = new BookingDataController();
        if (isset($generalConfiguration->onride_waiting_button) && $generalConfiguration->onride_waiting_button == 1 && count($price_card) > 0) {
            if ($request->type == 2) {
                if ($booking->onride_waiting_type == 1) {
                    $message = trans('api.already_ride_pause');
                    return response()->json(['result' => "0", 'message' => $message, 'type' => $request->type]);
                }
                $booking->onride_pause_timestamp = $date_time;
                $booking->onride_waiting_type = 1;
                $booking->save();
                $message = trans('api.ride_pause');
                //send notification to user
                $data = $bookingData->BookingNotification($booking);
                $notif_message = trans('api.user_ride_pause');
                Onesignal::UserPushMessage($booking->user_id, $data, $notif_message, 33, $booking->merchant_id);
                $type = 1;
            } elseif ($request->type == 1) {
                if ($booking->onride_waiting_type == 2) {
                    $message = trans('api.already_ride_start');
                    return response()->json(['result' => "0", 'message' => $message, 'type' => $request->type]);
                }
                $arriveTimeStemp = strtotime($booking->onride_pause_timestamp);
                $booking->onride_pause_timestamp = $date_time;
                $waitTime = round(abs($arriveTimeStemp - $startTimeStemp) / 60, 2);
                $booking->onride_waiting_time += $waitTime;
                $booking->onride_waiting_type = 2;
                $booking->save();
                $message = trans('api.ride_start');
                $data = $bookingData->BookingNotification($booking);
                $notif_message = trans('api.user_ride_resume');
                Onesignal::UserPushMessage($booking->user_id, $data, $notif_message, 33, $booking->merchant_id);
                $type = 2;
            }
        }
        return response()->json(['result' => "1", 'message' => $message, 'type' => $type]);
    }

    public function checkBookingOutstanding($user_id)
    {
        $outstanding = Outstanding::where(['user_id' => $user_id, 'pay_status' => 0, 'reason' => 2])->first();
        if ($outstanding) {
            $booking = Booking::with('BookingDetail')->findOrFail($outstanding->booking_id);
            $data['pickup_location'] = $booking->BookingDetail->start_location;
            $data['drop_location'] = $booking->BookingDetail->end_location;
            $data['booking_id'] = (string)$booking->id;
            $data['amount'] = (string)$outstanding->amount;
            $data['iso_code'] = $booking->CountryArea->Country->isoCode;
            $data['outstanding_id'] = $outstanding->id;
            $data['pay_later_payment'] = true;
            return $data;
        }
        return [];
    }


    // get list of ongoing bookings of driver
    public function getOngoingBookings(Request $request)
    {
        $data = [];
        try {
            $booking_obj = new Booking;
            $bookings = $booking_obj->getDriverOngoingBookings($request);
            $string_file = $this->getStringFile($request->merchant_id);
            foreach ($bookings as $booking) {
                $merchant_segment = $booking->Segment->Merchant->where('id', $booking->merchant_id);
                $merchant_segment = collect($merchant_segment->values());
                $booking_info = [
                    'id' => $booking->id,
                    'status' => $booking->booking_status,
                    'segment_name' => $booking->Segment->Name($booking->merchant_id) . ' ' . trans("$string_file.ride"),
                    'segment_slug' => $booking->Segment->slag,
                    'segment_group_id' => $booking->Segment->segment_group_id,
                    'segment_sub_group' => $booking->Segment->sub_group_for_app,
                    'number' => $booking->merchant_booking_id,
                    'segment_service' => $booking->ServiceType->ServiceName($booking->merchant_id),
                    'time' => $booking->booking_timestamp,
                    'segment_image' => isset($merchant_segment[0]['pivot']->segment_icon) && !empty($merchant_segment[0]['pivot']->segment_icon) ? get_image($merchant_segment[0]['pivot']->segment_icon, 'segment', $booking->merchant_id, true, false) :
                        get_image($booking->Segment->icon, 'segment_super_admin', NULL, false, false)
                ];
                $user_info = [
                    'user_name' => $booking->User->first_name . ' ' . $booking->User->last_name,
                    'user_image' => get_image($booking->User->UserProfileImage, 'user', $booking->merchant_id),
                    'user_phone' => $booking->User->UserPhone,
                    'user_rating' => "4.5",
                ];

                $pick_details = [
                    'lat' => $booking->pickup_latitude,
                    'lng' => $booking->pickup_longitude,
                    'address' => $booking->pickup_location,
                    'icon' => view_config_image("static-images/pick-icon.png"),

                ];
                $drop_details = [
                    'lat' => $booking->drop_latitude,
                    'lng' => $booking->drop_longitude,
                    'address' => $booking->drop_location,
                    'icon' => view_config_image("static-images/drop-icon.png"),
                ];
                $payment_details = [
                    'payment_mode' => $booking->PaymentMethod->payment_method,
                    'amount' => $booking->User->Country->isoCode . ' ' . $booking->estimate_bill,
                    'paid' => false
                ];

                $data[] = [
                    'info' => $booking_info,
                    'user_info' => $user_info,
                    'pick_details' => $pick_details,
                    'drop_details' => $drop_details,
                    'payment_details' => $payment_details,
                ];
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            return $message;
        }
        return $data;
    }

    public function endBooking(Request $request)
    {
        $booking_transaction_submit = NULL;
        DB::beginTransaction();
        try {
            $billDetails = '';
            $driver = $request->user('api-driver');
            $merchant_id = $driver->merchant_id;
            $validator = Validator::make($request->all(), [
                'booking_id' => [
                    'required',
                    'integer',
                    Rule::exists('bookings', 'id')->where(function ($query) {
                        $query->where('booking_status', 1004);
                    }),
                ],
                'latitude' => 'required',
                'longitude' => 'required',
                'tip_amount' => 'nullable|numeric',
            ]);
            if ($validator->fails()) {
                $errors = $validator->messages()->all();
                return $this->failedResponse($errors[0]);
            }
            $configuration = $driver->Merchant->BookingConfiguration;
            $config = $driver->Merchant->Configuration;

            $socket_enable = false;
            if ($config->lat_long_storing_at == 2) {
                $validator = Validator::make($request->all(), [
//                    'booking_polyline' => 'required',
                ]);
                if ($validator->fails()) {
                    $errors = $validator->messages()->all();
                    return $this->failedResponse($errors[0]);
//                    return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
                }
                $socket_enable = true;
            }

            $appConfig = $driver->Merchant->ApplicationConfiguration;
            $booking_id = $request->booking_id;
            $key = $configuration->google_key;

            $booking = Booking::with('PriceCard', 'BookingCoordinate')->find($booking_id);
            $string_file = $this->getStringFile(NULL, $booking->Merchant);
            // this configuration is for delivery segment
            if ($booking->Segment->slag == "DELIVERY" && isset($configuration->delivery_drop_otp) && ($configuration->delivery_drop_otp == 1 || $configuration->delivery_drop_otp == 2)) {
                self::deliveryDetailStore($request, $booking, $configuration->delivery_drop_otp);
            }

            if ($config->outside_area_ratecard == 1) {
                $area = CountryArea::find($booking->country_area_id);
                $ploygon = new PolygenController();
                $checkArea = $ploygon->CheckArea($request->latitude, $request->longitude, $area->AreaCoordinates);
                $price_card = PriceCard::select('id')->where(function ($query) use ($booking, $checkArea) {
                    $query->where([['merchant_id', '=', $booking->merchant_id], ['service_type_id', '=', $booking->service_type_id], ['country_area_id', '=', $booking->country_area_id], ['vehicle_type_id', '=', $booking->vehicle_type_id], ['status', '=', 1]]);
                    if (!$checkArea) {
                        $query->where('rate_card_scope', 2);
                    } else {
                        $query->where('rate_card_scope', 1);
                    }
                })->first();
                if (!empty($price_card)) {
                    $price_card_id = $price_card->id;
                    $booking->price_card_id = $price_card_id;
                    $booking->save();
                    $booking = $booking->fresh();
                }
            }

            $outstation_inside_City = (isset($configuration->outstation_inside_city) && $configuration->outstation_inside_city == 1) ? true : false;
            $bookingDetails = self::storeBookingDetails($request, $booking, $outstation_inside_City, $key, $socket_enable);

            $pricing_type = $booking->PriceCard->pricing_type;
            $price_card_id = $booking->price_card_id;
            $service_type_id = $booking->service_type_id;
            $service_type = $booking->ServiceType->type;

            $start_timestamp = $bookingDetails->start_timestamp;
            $endTimeStamp = $bookingDetails->end_timestamp;
            $seconds = $endTimeStamp - $start_timestamp;
            $hours = floor($seconds / 3600);
            $mins = floor($seconds / 60 % 60);
            $secs = floor($seconds % 60);
            $timeFormat = sprintf('%02d H %02d M', $hours, $mins, $secs);
            $rideTime = round(abs($endTimeStamp - $start_timestamp) / 60, 2);
            $from = $bookingDetails->start_latitude . "," . $bookingDetails->start_longitude;
            $to = $request->latitude . "," . $request->longitude;
            $coordinates = "";
            $bookingData = new BookingDataController();
            switch ($service_type) {
                case "1":
                    if(!empty($request->app_distance)){
                        $distance = $request->app_distance;
                    }else{
                        $bookingcoordinates = BookingCoordinate::where([['booking_id', '=', $request->booking_id]])->first();
                        $pick = $booking->pickup_latitude . "," . $booking->pickup_longitude;
                        $drop = $booking->drop_latitude . "," . $booking->drop_longitude;
                        $distanceCalculation = new DistanceCalculation();
                        $booking_coordinates = isset($bookingcoordinates['coordinates']) ? $bookingcoordinates['coordinates'] : "";
                        $distance = $distanceCalculation->distance($from, $to, $pick, $drop, $booking_coordinates, $merchant_id, $key, "endRide$booking_id", $string_file);
                        $distance = round($distance);
                        if ($socket_enable == true) {
                            $coordinates = $this->decodeValue($bookingcoordinates['booking_polyline']);
                        } else {
                            $coordinates = $booking_coordinates;
                        }
//                    $coordinates = $bookingcoordinates['coordinates'];
                    }
                    break;
                case "5":
                    $units = ($booking->CountryArea->Country['distance_unit'] == 1) ? 'metric' : 'imperial';
                    $distance = GoogleController::GoogleShortestPathDistance($from, $to, $key, $units, 'metric', "endRide$booking_id", $string_file);
                    $distance = round($distance);
                    break;
                default:
                    $distance = $bookingDetails->end_meter_value - $bookingDetails->start_meter_value;
                    $distance = $distance * 1000;
            }

            $merchant = new \App\Http\Controllers\Helper\Merchant();
            $tax_charge = 0;
            $hotel_amount = 0;
            switch ($pricing_type) {
                case "1":
                case "2":
                    // When estimate bill is equals to final bill
                    if ($configuration->final_bill_calculation == 2) {
                        $bill_details_data = [];
                        $tip_And_toll = self::storeTipAndTollCharge($appConfig, $request, $booking, $bookingDetails);
                        $booking->fresh();

                        $tip_amount = $tip_And_toll['tip_amount'];
                        if (empty($booking->bill_details)) {
                            throw new Exception("Bill details not found.");
                        }
                        $BillDetails = $this->EstimateBillDetailsBreakup($booking->bill_details, $booking->estimate_bill);
                       // $amount = $booking->estimate_bill;
                        $amount = $BillDetails['amount'];
                        $hotel_amount = $BillDetails['hotel_amount'];
                        if (!empty($booking->price_for_ride) && $booking->price_for_ride != 1) {
                            $BillDetails = $bookingData->checkPriceForRide($booking, $BillDetails);
                            $booking->bill_details = json_encode($BillDetails['bill_details'], true);
                            $booking->save();
                            $hotel_amount = 0;  // in case of maximum fare and fix fare
                        }

                        $total_payable = $merchant->FinalAmountCal(($amount), $merchant_id) + $bookingDetails->tip_amount;
                        Outstanding::where(['user_id' => $booking->user_id, 'reason' => '1', 'pay_status' => 0])->update(['pay_status' => 1]);
                        $bookingFee = $BillDetails['booking_fee'];
                        $amount_for_commission = $BillDetails['subTotalWithoutDiscount'] - $bookingFee;
                        $billDetails = json_encode($BillDetails['bill_details'], true);
                    } else {
                        $outstanding_amount = Outstanding::where(['user_id' => $booking->user_id, 'reason' => '1', 'pay_status' => 0])->sum('amount');
//                        $priceCard = PriceCard::find($price_card_id);
//                        date_default_timezone_set($priceCard->CountryArea->timezone);
                        // creating time zone issues

                        $finalBill = new PriceController();
                        $BillDetails = $finalBill->BillAmount([
                            'price_card_id' => $price_card_id,
                            'merchant_id' => $merchant_id,
                            'distance' => $distance,
                            'time' => $rideTime,
                            'booking_id' => $booking_id,
                            'user_id' => $booking->user_id,
                            'driver_id' => $booking->driver_id,
                            'booking_time' => $booking->booking_type == 2 ? $booking->later_booking_time : $booking->created_at->toTimeString(),
                            'booking_date' => $booking->booking_type == 2 ? $booking->later_booking_date : null,
                            'waitTime' => $bookingDetails->wait_time,
                            'dead_milage_distance' => $bookingDetails->dead_milage_distance,
                            'outstanding_amount' => $outstanding_amount,
                            'number_of_rider' => $booking->number_of_rider,
                            'from' => $from,
                            'to' => $to,
                            'coordinates' => $coordinates,
                            'units' => $booking->CountryArea->Country['distance_unit'],
                            'manual_toll_charge' => isset($request->manual_toll_charge) ? $request->manual_toll_charge : '',
                            'hotel_id' => !empty($booking->hotel_id) ? $booking->hotel_id : NULL,
                            'additional_movers' => !empty($booking->additional_movers) ? $booking->additional_movers : NULL,
                        ]);
                        $amount = $BillDetails['amount'];

                        Outstanding::where(['user_id' => $booking->user_id, 'reason' => '1', 'pay_status' => 0])->update(['pay_status' => 1]);
                        // amount => Sub total without discount + extra charges(night time /peck time) + surge charge - promo + taxs + insurance + toll charges +  cancellation_amount_received
                        if (!empty($booking->price_for_ride)) {
                            $BillDetails = $bookingData->checkPriceForRide($booking, $BillDetails);
                        }
                        $total_payable = $merchant->FinalAmountCal(($amount), $merchant_id) + $bookingDetails->tip_amount;

                        $bookingFee = $BillDetails['booking_fee'];
                        $amount_for_commission = $BillDetails['subTotalWithoutDiscount'] - $bookingFee;

                        $newArray = $BillDetails['bill_details'];
                        $bookingDetails->promo_discount = $BillDetails['promo'];
                        if ($appConfig->tip_status == 1) {
                            $tip_parameter = array('price_card_id' => $price_card_id, 'booking_id' => $booking_id, 'parameter' => "Tip", 'parameterType' => "tip_amount", 'amount' => $bookingDetails->tip_amount, 'type' => "CREDIT", 'code' => "");
                            array_push($newArray, $tip_parameter);
                        }
                        $billDetails = json_encode($newArray);
                        $hotel_amount = $BillDetails['hotel_amount'];
                    }
                    $amount = $amount + $bookingDetails->tip_amount + $hotel_amount;
                    $amount = $merchant->FinalAmountCal($amount, $merchant_id);

                    $booking_transaction_submit = BookingTransaction::updateOrCreate([
                        'booking_id' => $booking_id,
                    ], [
                        'date_time_details' => date('Y-m-d H:i:s'),
                        'sub_total_before_discount' => $amount_for_commission,
                        'surge_amount' => $BillDetails['surge'],
                        'extra_charges' => $BillDetails['extracharge'],
                        'discount_amount' => $BillDetails['promo'],
                        'tax_amount' => $BillDetails['total_tax'],
                        'tip' => $bookingDetails->tip_amount,
                        'insurance_amount' => $BillDetails['insurnce_amount'],
                        'cancellation_charge_received' => $BillDetails['cancellation_amount_received'],
                        'cancellation_charge_applied' => '0.0',
                        'toll_amount' => $BillDetails['toolCharge'],
                        'booking_fee' => $BillDetails['booking_fee'],
                        'cash_payment' => ($booking->PaymentMethod->payment_method_type == 1) ? $total_payable : '0.0',
                        'online_payment' => ($booking->PaymentMethod->payment_method_type == 1) ? '0.0' : $total_payable,
                        'customer_paid_amount' => $total_payable,
                        'rounded_amount' => round_number(($total_payable - $amount)),
                        'merchant_id'=>$booking->merchant_id
                    ]);

                    $bookingDetails->total_amount = $amount;

                    // company earning will deduct from driver account
                    $commission_data = CommonController::NewCommission($booking_id, $booking_transaction_submit['sub_total_before_discount']);

                    $booking_transaction_submit->company_earning = $commission_data['company_cut'];
                    $booking_transaction_submit->driver_earning = $commission_data['driver_cut'];

                    // revenue of driver
                    // Driver Commission + Discount Amt + tip + toll
                    $booking_transaction_submit->driver_total_payout_amount = $commission_data['driver_cut'] + $booking_transaction_submit->tip + $booking_transaction_submit->toll_amount + $booking_transaction_submit->discount_amount;

                    // revenue of merchant
                    // Company Commission + Tax Amt - Discount + Insurance Amt
                    $booking_transaction_submit->company_gross_total = $commission_data['company_cut'] + $booking_transaction_submit->tax_amount - $booking_transaction_submit->discount_amount + $booking_transaction_submit->insurance_amount + $booking_transaction_submit['cancellation_charge_received'];

                    // $booking_transaction_submit->trip_outstanding_amount = $merchant->TripCalculation(($booking_transaction_submit->driver_total_payout_amount + $booking_transaction_submit->amount_deducted_from_driver_wallet - $booking_transaction_submit->cash_payment), $merchant_id);
                    $booking_transaction_submit->trip_outstanding_amount = 0;
                    $booking_transaction_submit->commission_type = $commission_data['commission_type'];
                    if ($booking->hotel_id != '') {
                        $booking_transaction_submit->hotel_earning = $commission_data['hotel_cut'];
                    }
                    // $booking_transaction_submit->amount_deducted_from_driver_wallet = ($commission_data['commission_type'] == 1) ? $commission_data['company_cut'] : $merchant->TripCalculation('0.0', $merchant_id);     //Commission Type: 1:Prepaid (==OR==) 2:Postpaid
//                    $booking_transaction_submit->amount_deducted_from_driver_wallet = $commission_data['company_cut'];     //Commission Type: 1:Prepaid (==OR==) 2:Postpaid
                    $booking_transaction_submit->amount_deducted_from_driver_wallet = $booking_transaction_submit->company_gross_total;

                    // Instant settlement For Stripe Connect / Paystack Split
                    if ($booking->payment_method_id == 2 && ($booking->Driver->sc_account_status == 'active' || (isset($booking->Driver->paystack_account_status) && $booking->Driver->paystack_account_status == 'active'))) {
                        $booking_transaction_submit->instant_settlement = 1;
                    } else {
                        $booking_transaction_submit->instant_settlement = 0;
                    }
                    $booking_transaction_submit->save();

                    //Referral Calculation
//                    $billDetails = self::checkReferral($booking, $billDetails, $amount);

                    $bookingDetails->bill_details = $billDetails;
                    $bookingDetails->save();
                    $payment = new Payment();
                    if ($amount > 0) {
                        $currency = $booking->CountryArea->Country->isoCode;
//                      $currency = $booking->CountryArea->Country->isoCode;
                        $array_param = array(
                            'booking_id' => $booking->id,
                            'payment_method_id' => $booking->payment_method_id,
                            'amount' => $amount,
                            'user_id' => $booking->user_id,
                            'card_id' => $booking->card_id,
                            'currency' => $currency,
                            'quantity' => 1,
                            'order_name' => $booking->merchant_booking_id,
                            'booking_transaction' => $booking_transaction_submit,
                            'driver_sc_account_id' => $booking->Driver->sc_account_id,
                            'driver_paystack_account_id' => $booking->Driver->paystack_account_id
                        );
                        $payment->MakePayment($array_param);
//                        $payment->MakePayment($booking->id, $booking->payment_method_id, $amount, $booking->user_id, $booking->card_id, $currency, $booking_transaction_submit, $booking->Driver->sc_account_id);
                        $booking = $booking->fresh();
                    } else {
                        $payment->UpdateStatus(['booking_id' => $booking->id]);
                    }

                    //Referral Calculation
                    $ref = new ReferralController();
                    $arr_params = array(
                        "segment_id" => $booking->segment_id,
                        "driver_id" => $booking->driver_id,
                        "user_id" => $booking->user_id,
                        "booking_id" => $booking->id,
                        "user_paid_amount" => $amount,
                        "driver_paid_amount" => $amount,
                        "check_referral_at" => "OTHER"
                    );
                    $ref->checkReferral($arr_params);

                    RewardPoint::incrementUserTripCount(User::find($booking->user_id));
                    if ($booking->User->outstanding_amount) {
                        User::where([['id', '=', $booking->user_id]])->update(['outstanding_amount' => NULL]);
                    }
                    if ($booking->Merchant->Configuration->cashback_module == 1):
                        $cashback = new CashbackController();
                        $cashback->ProvideCashback($booking->country_area_id, $booking->service_type_id, $booking->vehicle_type_id, $booking, $amount);
                    endif;
                    break;
                case "3":
                    $amount = "";
                    break;
            }
            if ($service_type_id == 5) {
                $poolRide = new PoolController();
                $poolRide->DropPool($booking, $request);
            }

            $distance_unit = $booking->CountryArea->Country->distance_unit;
            $div = $distance_unit == 1 ? 1000 : 1609;
            $distance = round($distance / $div, 2) . ' ' . ($distance_unit == 1 ? 'Km' : 'mi');

            $booking->booking_status = 1005;
            if (isset($appConfig->user_rating_enable) && $appConfig->user_rating_enable != 1 && $pricing_type != 3 && $booking->payment_status == 1) {
                $booking->booking_closure = 1;
            }
            $booking->travel_distance = $distance;
            $booking->travel_time = $timeFormat;
            $booking->travel_time_min = $rideTime;
            $booking->final_amount_paid = $amount;
            $booking->bill_details = $billDetails;
            $booking->save();

            if ($service_type_id == 5) {
                $runningBooking = Booking::where([['service_type_id', 5], ['driver_id', $booking->driver_id]])->whereIn('booking_status', [1001, 1002, 1003, 1004])->latest()->first();
                if (empty($runningBooking)) {
                    $free_busy = 2;
                }
            } else {
                $free_busy = 2;
            }

            $driver = $request->user('api-driver');
            $driver->total_trips = $request->user('api-driver')->total_trips + 1;
            $driver->free_busy = $free_busy;
            $driver->save();

            $user = User::find($booking->user_id);
            $user->total_trips = $user->total_trips + 1;
            $user->save();
            $booking = $booking->fresh();

            // insert booking status history
            $this->saveBookingStatusHistory($request, $booking, $booking->id);

            if ($driver->Merchant->Configuration->subscription_package == 1 && $driver->subscription_wise_commission == 1) {
                $this->SubscriptionPackageExpiryCheck($driver);
            }

            if (in_array($pricing_type, [1, 2])) {
                //                $data = $bookingData->BookingNotification($booking);
//                $message = $bookingData->LanguageData($booking->merchant_id, 34);
//                Onesignal::UserPushMessage($booking->user_id, $data, $message, 1, $booking->merchant_id);

                $SmsConfiguration = SmsConfiguration::select('ride_end_enable', 'ride_end_msg')->where([['merchant_id', '=', $merchant_id]])->first();
                if (!empty($SmsConfiguration) && $SmsConfiguration->ride_end_enable == 3) {
                    $sms = new SmsController();
                    $phone = $booking->User->UserPhone;
                    $sms->SendSms($merchant_id, $phone, null, 'RIDE_END', $booking->User->email);
                }
            }
            if ($booking->payment_status == 1) {
                try {
                    // event(new SendUserInvoiceMailEvent($booking));
//                    event(new SendUserInvoiceMailEvent($booking, 'invoice'));
                } catch (Exception $e) {
                    p($e->getMessage());
                }

            } else {
                // commented by amba
//                $paymentMethods = $booking->CountryArea->PaymentMethod->toArray();
//                $is_cash_available = in_array(1, array_column($paymentMethods, 'id'));
                // If booking payment method is card and merchant not have any case payment method then.
                //Editing by @Amba, out standing will be creating when user click on paylater from receipt screen
//                if (isset($config->user_outstanding_enable) && $config->user_outstanding_enable == 1 && $booking->payment_method_id == 2 && $is_cash_available != 1) {
//                    // Later pay before next ride.
//                    CommonController::AddUserRideOutstading($booking->user_id, $booking->driver_id, $booking->id, $amount);
//                    $payment->UpdateStatus(['booking_id' => $booking->id]);
//                    BookingDetail::where([['booking_id', '=', $booking->id]])->update(['pending_amount' => $amount]);
//                }
            }
//            return $this->successResponse(trans('api.message15'), $booking);
        } catch (Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        DB::commit();

        // clear the call masking session
        if (isset($config->twilio_call_masking) && $config->twilio_call_masking == 1) {
            TwilioMaskingHelper::close_session($booking);
        }

        // send notification after commit
        $notification_data = $bookingData->bookingNotificationForUser($booking, "END_RIDE");
        $booking_data = new BookingDataController;
        $return_data = $booking_data->bookingReceiptForDriver($request);
        // call
        $this->updateRideAmountInDriverWallet($booking, $booking_transaction_submit, $booking->id);
        return $this->successResponse(trans("$string_file.success"), $return_data);
    }

    public function updateRideAmountInDriverWallet($booking = NULL, $booking_transaction = NULL, $booking_id = NULL)
    {
        // Make the payment of driver instant
        /** This code will be executed when payment is done (wallet or card payment)
         ***/
        if ($booking->payment_status == 1) {
            $driverPayment = new CommonController();
            $array_param = array(
                'booking_id' => $booking->id,
                'driver_id' => $booking->driver_id,
//            'amount' => $amount,
                'payment_method_type' => $booking->PaymentMethod->payment_method_type,
//            'discount_amount' => $booking_transaction_submit->discount_amount,
//            'tax_amount' => $booking_transaction_submit->tax_amount,
//            'cancellation_amount_received' => $BillDetails['cancellation_amount_received'],
            );
            if (empty($booking) && !empty($booking_id)) {
                $booking = Booking::find($booking_id);
                $booking_transaction = $booking->BookingTransaction;
            }
//    || $booking->PaymentMethod->payment_method_id == 3
            // debit drive wallet for merchant commission
            if ($booking->payment_method_id == 1 || $booking->payment_method_id == 5) // cash or swipe card payment
            {
                $array_param['amount'] = $booking_transaction->company_gross_total;
                $array_param['wallet_status'] = 'DEBIT';
                $array_param['narration'] = 3;
            } else // online payment like card, payment gateway, wallet.
            {
                $array_param['amount'] = $booking_transaction->driver_total_payout_amount;
                $array_param['wallet_status'] = 'CREDIT';
                $array_param['narration'] = 6;
            }
            $driverPayment->DriverRideAmountCredit($array_param);

            // If driver payment slited with payment gateway like stripe connect and paytack, then debit credited amount
            if($booking_transaction->instant_settlement == 1){
                $new_array_param['booking_id'] = $booking->id;
                $new_array_param['driver_id'] = $booking->driver_id;
                $new_array_param['amount'] = $booking_transaction->driver_total_payout_amount;
                $new_array_param['wallet_status'] = 'DEBIT';
                $new_array_param['narration'] = 25;
                $driverPayment->DriverRideAmountCredit($new_array_param);
            }
        }
    }

    // $delivery_drop_condition - it should be 1 for otp and 2 for qr code
    public function deliveryDetailStore($request, $booking, $delivery_drop_condition = 1)
    {
        $validate_arr = [
            'receiver_otp' => 'required',
        ];
        if ($delivery_drop_condition == 1) {
            $validate_arr = array_merge($validate_arr, array(
                'receiver_otp' => 'required',
                'receiver_name' => 'required',
                'receiver_image' => 'required',
            ));
            $validator = Validator::make($request->all(), $validate_arr);
        }
        if ($delivery_drop_condition == 2) {
            $validator = Validator::make($request->all(), $validate_arr);
        }
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new Exception($errors[0]);
        }

        $booking_delivery_detail = BookingDeliveryDetails::where(['booking_id' => $booking->id, 'otp_status' => 0, 'drop_status' => 0])->orderBy('stop_no', 'DESC')->first();
        $driver = $request->user('api-driver');
        $string_file = $this->getStringFile(NULL, $driver->Merchant);
        if (!empty($booking_delivery_detail)) {
            if ($request->receiver_otp == $booking_delivery_detail->opt_for_verify) {
                $booking_delivery_detail->otp_status = 1;
                $booking_delivery_detail->drop_status = 1;
                $booking_delivery_detail->receiver_name = $request->receiver_name;
                $booking_delivery_detail->receiver_phone = isset($request->receiver_phone) ? $request->receiver_phone : '';
                if (!empty($request->receiver_image)) {
                    $booking_delivery_detail->receiver_image = $this->uploadBase64Image('receiver_image', 'booking_images', $booking->merchant_id);
                }
//                    $this->uploadImage('receiver_image', 'booking_images', $booking->merchant_id);
                $booking_delivery_detail->drop_latitude = $request->latitude;
                $booking_delivery_detail->drop_longitude = $request->longitude;
                $booking_delivery_detail->save();
            } else {
                throw new Exception(trans("$string_file.invalid_otp_try_again"));
            }
        }
    }

    public function storeBookingDetails($request, $booking, $outstation_inside_City, $key, $socket_enable = false)
    {
        try {
//            $service_type_id = $booking->service_type_id;
            $service_type = $booking->ServiceType->type;
            $bookingDetails = BookingDetail::where('booking_id', $booking->id)->first();
            $string_file = $this->getStringFile($booking->merchant_id);
            if (in_array($service_type, array(2, 4)) && $outstation_inside_City == false) {
                $start_meter_value = $bookingDetails->start_meter_value;
                $customMessages = [
                    'gt' => trans_choice(trans("$string_file.end_meter_warning"), 3, ['value' => $start_meter_value]),
                ];
                $validator = Validator::make($request->all(), [
                    'send_meter_image' => 'required',
                    'send_meter_value' => 'required|numeric|gt:' . $start_meter_value,
                ], $customMessages);
                if ($validator->fails()) {
                    $errors = $validator->messages()->all();
                    throw new Exception($errors[0]);
                }
            }
            if (!empty($request->send_meter_image)) {
                $send_meter_image = $this->uploadBase64Image('send_meter_image', 'send_meter_image', $booking->merchant_id);
                $bookingDetails->end_meter_image = $send_meter_image;
            }
            $bookingDetails->end_meter_value = $request->send_meter_value;
            if ($outstation_inside_City == true) {
                $bookingDetails->end_meter_value = 1200;
            }
            $endAddress = GoogleController::GoogleLocation($request->latitude, $request->longitude, $key, 'endRide', $string_file);
            $endAddress = $endAddress ? $endAddress : 'Address Not found';
            $endTimeStamp = strtotime('now');
            $bookingDetails->end_timestamp = $endTimeStamp;
            $bookingDetails->end_latitude = $request->latitude;
            $bookingDetails->end_longitude = $request->longitude;
            $bookingDetails->end_location = $endAddress;
            $bookingDetails->accuracy_at_end = $request->accuracy;
            $bookingDetails->save();

            // booking polyline
            if ($socket_enable == true) {
                $booking_coordinates = $booking->BookingCoordinate;
                if (empty($booking->BookingCoordinate->id)) {
                    $booking_coordinates = new BookingCoordinate;
                    $booking_coordinates->booking_id = $booking->id;
                }
                $booking_coordinates->booking_polyline = $request->booking_polyline;
                $booking_coordinates->save();
            }

            return $bookingDetails;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function storeTipAndTollCharge($appConfig, $request, $bookingObj, $bookingDetails)
    {
        try {
            $bill_details_data = [];
            $booking = Booking::find($bookingObj->id);
            if (!empty($booking->bill_details)) {
                $bill_details_data = json_decode($booking->bill_details, true);
            }
            $toll_charge = isset($request->manual_toll_charge) ? $request->manual_toll_charge : 0;
            // Update Toll Amount
            foreach ($bill_details_data as $key => $value) {
                if (array_key_exists('parameter', $value) && $value['parameter'] == "TollCharges") {
                    $bill_details_data[$key]['amount'] = $toll_charge;
                }
            }
            // Add Tip amount in bill details if tip exist in booking
            $tip_amount = 0;
            if ($appConfig->tip_status == 1) {
                $tip_amount = (!empty($bookingDetails->tip_amount) && ($bookingDetails->tip_amount != null)) ? $bookingDetails->tip_amount : '0.0';
                $tip_parameter = array('price_card_id' => $bookingObj->price_card_id, 'booking_id' => $bookingObj->id, 'parameter' => "Tip", 'parameterType' => "tip_amount", 'amount' => $tip_amount, 'type' => "CREDIT", 'code' => "");
                array_push($bill_details_data, $tip_parameter);
            }
            $booking->bill_details = json_encode($bill_details_data, true);
            $booking->save();
            return array('tip_amount' => $tip_amount, 'toll_amount' => $toll_charge);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
