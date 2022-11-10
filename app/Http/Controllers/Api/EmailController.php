<?php

namespace App\Http\Controllers\Api;

use App\Events\SendUserInvoiceMailEvent;
use App\Models\Booking;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Traits\MerchantTrait;

class EmailController extends Controller
{
    use MerchantTrait, ApiResponseTrait;

    public function Invoice(Request $request, $booking_id)
    {
        try {
            $request->request->add(['booking_id' => $booking_id]);
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
//                return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
            }
            $booking = Booking::find($request->booking_id);
            $booking->another_email = isset($request->email) ? $request->email : null;
            event(new SendUserInvoiceMailEvent($booking, 'invoice'));
            $string_file = $this->getStringFile(NULL, $booking->Merchant);
            return $this->successResponse(trans("$string_file.email_sent_successfully"));
//            return response()->json(['result' => "1", 'message' => trans("$string_file.email_sent_successfully"), 'data' => []]);
        } catch (\Exception $exception) {
            return $this->failedResponse($exception->getMessage());
        }
    }
}
