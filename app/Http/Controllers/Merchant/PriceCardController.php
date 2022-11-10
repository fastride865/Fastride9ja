<?php

namespace App\Http\Controllers\Merchant;

use App\Models\ApplicationConfiguration;
use App\Models\Configuration;
use App\Models\CountryArea;
use App\Models\InfoSetting;
use App\Models\PriceCardDetail;
use App\Models\ServicePackage;
use App\Models\OutstationPackage;
use App\Models\ExtraCharge;
use App\Models\Merchant;
use App\Models\PriceCardCommission;
use App\Traits\AreaTrait;
use App\Traits\PriceTrait;
use App\Traits\MerchantTrait;
use Auth;
use App\Models\PriceCardValue;
use App\Models\PricingParameter;
use App\Models\PriceCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use View;
use DB;
use App\Http\Controllers\Helper\AjaxController;

class PriceCardController extends Controller
{
    use AreaTrait, PriceTrait, MerchantTrait;

    public function __construct()
    {
//        $info_setting = InfoSetting::where('slug','TAXI_LOGISTICS_SERVICE_PRICE_CARD')->first();
//        view()->share('info_setting', $info_setting);
    }

    /**Start price card functionality of Taxi Related segment */

    // this function will be used for searching also
    public function index(Request $request)
    {
        $area_id = null;
        if (isset($request->area) && !empty($request->area)) {
            $area_id = $request->area;
        }
        $checkPermission = check_permission(1, ['price_card_TAXI','price_card_DELIVERY'],true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $pricecards = $this->getPriceList(true, false, $area_id);
        $merchant = get_merchant_id(false); // means get the logged in merchant object
        $config = $merchant->ApplicationConfiguration;
        $string_file = $this->getStringFile(NULL,$merchant);
        $config->driver_wallet_status = $merchant->Configuration->driver_wallet_status;
        $config->company_admin = $merchant->Configuration->company_admin;
        $config->geofence_module = $merchant->Configuration->geofence_module;
        $config->hotel_admin = $merchant->hotel_active;
        $areas = $this->getMerchantCountryArea($this->getAreaList(false, true)->get(), 1, 1,$string_file);
        $data = [];
        $info_setting = InfoSetting::where('slug','TAXI_LOGISTICS_SERVICE_PRICE_CARD')->first();
        return view('merchant.pricecard.index', compact('pricecards', 'areas', 'config', 'area_id', 'merchant', 'data','info_setting'));
    }

    /**
     * Add Edit form of price card
     */
    public function add(Request $request, $id = null)
    {
        $checkPermission = check_permission(1, ['price_card_TAXI','price_card_DELIVERY'],true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }

        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $configuration = $merchant->Configuration;
        $config = $merchant->ApplicationConfiguration;
        $additional_mover_config = $merchant->BookingConfiguration->additional_mover;
        $geo_fence = true;
        $config->driver_wallet_status = $merchant->Configuration->driver_wallet_status;
        $config->insurance_enable = $merchant->BookingConfiguration->insurance_enable;
//        $days = get_days($string_file);
        $days = \Config::get('custom.days');
        $price_card = [];
        $selected_segment = [];
        $input_html = '';
        $package_name = '';
        $is_demo = false;
        if (!empty($id)) {
            $price_card = PriceCard::with('Segment', 'CountryArea', 'ExtraCharges')->where([['status', 1]])->findorfail($id);
            $pre_title = trans("$string_file.add");
            $submit_button = trans("$string_file.update");
            $is_demo =  ($merchant->demo == 1 && $price_card->country_area_id == 3) ? true : false;
            $request->request->add(['type' => $price_card->pricing_type, 'call_from' => 'function', 'segment_id' => $price_card->segment_id]);
            $input_html = $this->getPricingParameter($request, $price_card);

            if (!empty($price_card->service_package_id)) {
                $additional_support = $price_card->ServiceType->additional_support;
                $package = $additional_support == 1 ? ServicePackage::Find($price_card->service_package_id) : OutstationPackage::Find($price_card->service_package_id);
                $package_name = $package->PackageName;
            }
        } else {
            $pre_title = trans("$string_file.edit");
            $submit_button = trans("$string_file.save");
        }
        $title = $pre_title.' '. trans("$string_file.price_card");
        $areas = $this->getMerchantCountryArea($this->getAreaList(false, true)->get(), $geo_fence, 1);
        $areas = add_blank_option($areas, trans("$string_file.service_area"));
        $segment_group_id = 1;
        $sub_group_for_admin = 1;
        $arr_segment = get_merchant_segment(true, NULL, $segment_group_id,$sub_group_for_admin);
        $info_setting = InfoSetting::where('slug','TAXI_LOGISTICS_SERVICE_PRICE_CARD')->first();
        return view('merchant.pricecard.form',
            compact('price_card', 'title', 'submit_button', 'areas',
                'merchant', 'config', 'days', 'configuration', 'arr_segment', 'input_html', 'package_name','info_setting','is_demo','additional_mover_config')
        );
    }

    /*save/update function*/
    public function save(Request $request, $id = null)
    {
//        p($request->all());
        $validator = Validator::make($request->all(), [
            'country_area_id' => 'required_without:id',
            'price_card_name' => 'required',
            'service_type_id' => 'required_without:id',
            'price_type' => 'required|integer',
//            'commission_type' => 'required',
            'commission_method' => 'required',
            'commission' => 'required',
            'outstation_type' => 'required_if:additional_support,==,2',
            'max_distance' => 'required_if:outstation_type,==,1',
            'input_provider' => 'required_if:price_type,==,3',
            'segment_id' => 'required_without:id',
            'package_id' => 'required_if:additional_support,==,1|required_if:outstation_type,==,2',
            'cancel_charges' => 'required_if:merchant_cancel_charges,==1',
            'cancel_time' => 'required_if:cancel_charges,==1',
            'cancel_amount' => 'required_if:cancel_charges,==1',
            'vehicle_type_id' => 'required_without:id',
        ]);
//        p($request->all());
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {
            if ($request->service_type_id == 1) {
                $request->package_id = null;
            }
            $merchant = get_merchant_id(false);
            $merchant_id = $merchant->id;
            $service = $request->service_type_id;
            $string_file = $this->getStringFile($merchant_id);

            if (!empty($id)) {
                $price_card = PriceCard::Find($id);
            } else {
                $q = PriceCard::where([['merchant_id', $merchant_id], ['service_type_id', $request->service_type_id], ['country_area_id', $request->country_area_id]]);
                if ($request->additional_support == 1 || ($request->additional_support == 2 && $request->outstation_type == 2)) {
                    $q->where('service_package_id', $request->package_id);
                }
                if (!empty($request->vehicle_type_id)) {
                    $q->where('vehicle_type_id', $request->vehicle_type_id);
                }
                if ($request->additional_support == 2) {
                    $q->where('outstation_type', $request->outstation_type);
                }
                if($merchant->Configuration->outside_area_ratecard == 1){
                    $q->where('rate_card_scope', $request->rate_card_scope);
                }
                $price_card = $q->first();

                if (!empty($price_card->id)) {
                    return redirect()->back()->withErrors(trans("$string_file.price_card_already_exist"));
                } else {
                    $price_card = new PriceCard;
                }
            }
            $this->savePriceCard($price_card, $merchant_id, $request);
        } catch (\Exception $e) {
            $message = $e->getMessage();
//            p($message);
            // Rollback Transaction
            DB::rollback();
            return redirect()->back()->withErrors($message);
        }
        DB::commit();
        return redirect()->route('pricecard.index')->withSuccess(trans("$string_file.saved_successfully"));
    }

    // save price card details
    public function savePriceCard($price_card, $merchant_id, $request)
    {
        try {
            $price_card->merchant_id = $merchant_id;
            $price_card->price_card_name = $request->price_card_name;
            if (!empty($request->price_card_for)) {
                $price_card->price_card_for = $request->price_card_for;
            }
            if (!empty($request->country_area_id)) {
                $price_card->country_area_id = $request->country_area_id;
            }
            if (!empty($request->service_type_id)) {
                $price_card->service_type_id = $request->service_type_id;
            }
            if (!empty($request->vehicle_type_id)) {
                $price_card->vehicle_type_id = $request->vehicle_type_id;
            }
            if (!empty($request->segment_id)) {
                $price_card->segment_id = $request->segment_id;
            }
            if (!empty($request->package_id)) {
                $price_card->service_package_id = $request->package_id;
            }
            if (!empty($request->outstation_type)) {
                $price_card->outstation_type = $request->outstation_type;
            }
            if (!empty($request->rate_card_scope)) {
                $price_card->rate_card_scope = $request->rate_card_scope;
            }
//            $price_card->rate_card_scope = $request->rate_card_scope;
            $price_card->pricing_type = $request->price_type;
            $price_card->outstation_max_distance = $request->max_distance;
//            $price_card->outstation_type = $request->additional_support == 2 ? $request->outstation_type : NULL;
            $price_card->maximum_bill_amount = $request->maximum_bill_amount;

            $price_card->base_fare = $request->base_fare;
            $price_card->free_distance = $request->free_distance;
            $price_card->free_time = $request->free_time;
            $price_card->extra_sheet_charge = $request->extra_sheet_charge;
            $price_card->minimum_wallet_amount = $request->minimum_wallet_amount;
            $price_card->cancel_charges = $request->cancel_charges;
            $price_card->cancel_time = $request->cancel_time;
            $price_card->cancel_amount = $request->cancel_amount;
            $price_card->sub_charge_type = $request->sub_charge_type;
            $price_card->sub_charge_value = $request->sub_charge_value;
            $price_card->sub_charge_status = $request->sub_charge_status;
//            $price_card->driver_cash_booking_limit = $request->driver_cash_booking_limit;
            $price_card->insurnce_enable = $request->insurnce_enable;
            $price_card->insurnce_type = $request->insurnce_type;
            $price_card->insurnce_value = $request->insurnce_value;
            $price_card->additional_mover_charge = $request->additional_mover_charge;
            $price_card->save();

            $this->addPricingParameter($request, $price_card->id);
//            if ($request->week_days && count($request->week_days) > 0) {
                $this->savePeakTimeCharges($request, $price_card->id);
//            }
//        $price_card->PaymentMethod()->sync($request->input('payment_method'));
//        $price_card->Segment()->sync($request->input('segment'));
            $this->saveCommission($price_card->id, $request);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // save commission of price card
    public function saveCommission($price_card_id, $request)
    {
        PriceCardCommission::updateOrCreate(['price_card_id' => $price_card_id], [
//            'commission_type' => $request->commission_type,
            'commission_method' => $request->commission_method,
            'commission' => $request->commission,
            'taxi_commission_type' => $request->taxi_commission_type,
            'taxi_commission_method' => $request->taxi_commission_method,
            'taxi_commission' => $request->taxi_commission,
            'hotel_commission_type' => $request->hotel_commission_type,
            'hotel_commission_method' => $request->hotel_commission_method,
            'hotel_commission' => $request->hotel_commission,
        ]);
    }

    // save additional param of price card
    public function addPricingParameter($request, $price_card_id)
    {
        try {
            PriceCardValue::where('price_card_id', $price_card_id)->delete();
            if ($request->price_type == 3) {
                foreach ($request->input_provider as $value) {
                    PriceCardValue::create(['pricing_parameter_id' => $value, 'parameter_price' => 1, 'price_card_id' => $price_card_id]);
                }
            } else {
                $check_box_values = $request->check_box_values;
                $checkboxArray = $request->checkboxArray;
                $checkboxFreeArray = $request->checkboxFreeArray;
                if (!empty($checkboxArray) && is_array($checkboxArray)) {
                    foreach ($checkboxArray as $key => $value) {
                        $para_value = isset($check_box_values[$key]) ? $check_box_values[$key] : NULL;
                        if($para_value > 0){
                            $free_amount = isset($checkboxFreeArray[$key]) ? $checkboxFreeArray[$key] : NULL;
                            PriceCardValue::create(['free_value' => $free_amount,
                                'pricing_parameter_id' => $value,
                                'parameter_price' => $para_value,
                                'price_card_id' => $price_card_id,
                                'parameter_edit' => '0']);
                        }else{
                            throw new \Exception('Parameter price should be greater than 0.');
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // save pick time charges of price card
    public function savePeakTimeCharges($request, $price_card_id)
    {
//        p($request->all());
        ExtraCharge::where('price_card_id', $price_card_id)->delete();

        if ($request->week_days && count($request->week_days) > 0) {
            foreach ($request->week_days as $i => $value) {
                if (isset($request->week_days[$i])) {
                    ExtraCharge::create([
                            'price_card_id' => $price_card_id,
                            'parameterName' => isset($request->parametername[$i]) ? $request->parametername[$i] : "",
                            'slot_week_days' => isset($request->week_days[$i]) ? implode(",", $request->week_days[$i]) : NULL,
                            'slot_start_time' => isset($request->begintime[$i]) ? $request->begintime[$i] : NULL,
                            'slot_end_time' => isset($request->endtime[$i]) ? $request->endtime[$i] : NULL,
                            'slot_charges' => isset($request->slot_charges[$i]) ? $request->slot_charges[$i] : NULL,
                            'slot_end_day' => isset($request->optradio[$i]) ? $request->optradio[$i] : NULL,
                            'slot_charge_type' => isset($request->charge_type[$i]) ? $request->charge_type[$i] : NULL
                        ]
                    );
                }
            }
        }
    }

    // get additional param of price card while edit
    public function getPricingParameter(Request $request, $price_card = NULL)
    {
        $html = $this->inputFieldDetails($request, $price_card);
        if ($request->call_from == 'function') {
            return $html;
        } else {
            echo $html;
        }
    }

    // get input by fields
    public function inputFieldDetails(Request $request, $price_card = NULL)
    {
        $html = '';
        $merchant_id = get_merchant_id();
        $string_file =$this->getStringFile($merchant_id);
        $val = $request->type;
        $arr_segment = $request->segment_id;
        $parameters = PricingParameter::whereHas('PricingType', function ($query) use ($val) {
            $query->where('price_type', $val);
        })->whereHas('Segment', function ($query) use ($arr_segment) {
            $query->where('segment_id', $arr_segment);
        })->where([['deleted_at', '=', NULL], ['merchant_id', '=', $merchant_id]])->orderBy('sequence_number', 'ASC')->get();
        $saved_parameter = [];
        if (isset($price_card->PriceCardValues) && !empty($price_card->PriceCardValues)) {
            $saved_parameter = $price_card->PriceCardValues->toArray();
        }
        $base_fare = NULL;
        $free_distance = NULL;
        $free_time = NULL;
        foreach ($parameters as $parameter) {
            $id = $parameter->id;
            $parameterName = $parameter->ParameterName;
            $parameterType = $parameter->parameterType;
            $checked = in_array($id, array_column($saved_parameter, 'pricing_parameter_id')) ? 'checked' : '';
            $checked_val = NULL;
            $disabled = '';
            $free_val = NULL;
            if (in_array($parameterType, [6, 9, 13])) {
                if (!empty($checked)) {
                    $key = array_search($id, array_column($saved_parameter, 'pricing_parameter_id'));
                    $free_val = isset($saved_parameter[$key]['free_value']) ? $saved_parameter[$key]['free_value'] : NULL;
                }
            }
            if ($parameterType == 10 && !empty($price_card->base_fare)) {
                $checked = "checked";
                $base_fare = $price_card->base_fare;
                $free_distance = $price_card->free_distance;
                $free_time = $price_card->free_time;
            } else {
                if (!empty($checked)) {
                    $key = array_search($id, array_column($saved_parameter, 'pricing_parameter_id'));
                    $checked_val = isset($saved_parameter[$key]['parameter_price']) ? $saved_parameter[$key]['parameter_price'] : NULL;
                }
            }
            if (empty($checked)) {
                $disabled = "disabled";
            }
            if ($val == 3) {
                $html .= '<div class="row">';
                $html .= '<div class="col-md-6">';
                $html .= '<div class="form-group">';
                $html .= '<label for="emailAddress5">' . $parameterName . ' :</label>';
                $html .= '<input type="checkbox" id="input_provider" name="input_provider[]" value="' . $id . '" '.$checked.'>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            } else {
                switch ($parameterType) {
                    case "6":
                        $html .= '<div class="row" style="margin-bottom: 10px">';
                        $html .= '<div class="col-md-4">';
                        $html .= '<label for="ProfileImage">' . $parameterName . ':</label>';
                        $html .= '<div class="input-group">';
                        $html .= '<div class="input-group-prepend">';
                        $html .= '<div class="input-group-text">';
                        $html .= '<div class="custom-control custom-checkbox">';
                        $html .= '<input type="checkbox" class="custom-control-input" onclick="invisibleInput(this.value)" id="' . $id . '" name="checkboxArray[' . $id . ']" value="' . $id . '"  '.$checked.'>';
                        $html .= ' <label class="custom-control-label" for="' . $id . '"></label>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<input value="' . $checked_val . '" type="number" step="0.01" min="0" class="form-control" id="test' . $id . '" onkeypress="return NumberInput(event)" name="check_box_values[' . $id . ']" placeholder="' . trans('admin.message164') . '" aria-describedby="checkbox-addon1" ' . $disabled . '>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="col-md-4">';
                        $html .= '<label for="ProfileImage">' . trans("$string_file.base_fare") . $parameterName . ':</label>';
                        $html .= '<div class="form-group">';
                        $html .= '<div class="input-group-prepend">';
                        $html .= ' <input type="number" value="' . $free_val . '" class="form-control"  step="0.01" min="0" id="checkboxFreeArray"  name="checkboxFreeArray[' . $id . ']"  placeholder="">';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        break;
                    case "10":
                        if (!empty($saved_parameter)):
                            $distance_unit_type = $price_card->CountryArea->Country['distance_unit'] == 1 ? trans($string_file.'.distance_in_km') : trans($string_file.'.distance_in_mile');
                        else:
                            $distance_unit_type = trans($string_file.'.distance_in_km') . '/' . trans("$string_file.miles");
                        endif;
                        $html .= '<div class="row" style="margin-bottom: 10px">';
                        $html .= '<div class="col-md-4">';
                        $html .= '<label for="ProfileImage"> ' . $parameterName . ':</label>';
                        $html .= '<div class="input-group">';
                        $html .= '<div class="input-group-prepend">';
                        $html .= '<div class="input-group-text">';
                        $html .= '<div class="custom-control custom-checkbox">';
                        $html .= '<input type="checkbox" class="custom-control-input" onclick="invisibleInput(this.value)" id="' . $id . '" name="basefareArray[' . $id . ']" value="' . $id . '" '.$checked.'>';
                        $html .= ' <label class="custom-control-label" for="' . $id . '"></label>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<input type="number" value="' . $base_fare . '" step="0.01" min="0" class="form-control" id="test' . $id . '" onkeypress="return NumberInput(event)" name="base_fare" placeholder="" aria-describedby="checkbox-addon1" ' . $disabled . '>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="col-md-4">';
                        $html .= '<label for="ProfileImage"> ' . trans($string_file.'.distance_included') .' '. $parameterName . ':</label>';
                        $html .= '<div class="form-group">';
                        $html .= '<div class="input-group-prepend">';
                        $html .= '<input type="number" step="0.01" value="' . $free_distance . '" class="form-control" min="0" id="freedistance" name="free_distance" placeholder="' . $distance_unit_type . '">';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="col-md-4">';
                        $html .= '<label for="ProfileImage"> ' . trans($string_file.'.time_included') . $parameterName . ':</label>';
                        $html .= '<div class="form-group">';
                        $html .= '<div class="input-group-prepend">';
                        $html .= ' <input type="number" value="' . $free_time . '" class="form-control" min="0" id="freetime"  name="free_time"  placeholder="" step="any">';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        break;
                    case "9":
                        $html .= '<div class="row" style="margin-bottom: 10px">';
                        $html .= '<div class="col-md-4">';
                        $html .= '<label for="ProfileImage">' . $parameterName . ':</label>';
                        $html .= '<div class="input-group">';
                        $html .= '<div class="input-group-prepend">';
                        $html .= '<div class="input-group-text">';
                        $html .= '<div class="custom-control custom-checkbox">';
                        $html .= '<input type="checkbox" class="custom-control-input" onclick="invisibleInput(this.value)" id="' . $id . '" name="checkboxArray[' . $id . ']" value="' . $id . '" '.$checked.'>';
                        $html .= ' <label class="custom-control-label" for="' . $id . '"></label>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<input type="number" value="' . $checked_val . '" step="0.01" min="0" class="form-control" id="test' . $id . '" onkeypress="return NumberInput(event)" name="check_box_values[' . $id . ']" placeholder="" aria-describedby="checkbox-addon1" ' . $disabled . '>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="col-md-4">';
                        $html .= '<label for="ProfileImage">' . trans($string_file.'.free_time_included') . ':</label>';
                        $html .= '<div class="form-group">';
                        $html .= '<div class="input-group-prepend">';
                        $html .= ' <input type="number" value="' . $free_val . '" min="0" class="form-control"  id="checkboxFreeArray"  name="checkboxFreeArray[' . $id . ']"  placeholder="">';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        break;
                    case "13":
                        $html .= '<div class="row" style="margin-bottom: 10px">';
                        $html .= '<div class="col-md-4">';
                        $html .= '<label for="ProfileImage">' . $parameterName . ':</label>';
                        $html .= '<div class="input-group">';
                        $html .= '<div class="input-group-prepend">';
                        $html .= '<div class="input-group-text">';
                        $html .= '<div class="custom-control custom-checkbox">';
                        $html .= '<input type="checkbox" class="custom-control-input" onclick="invisibleInput(this.value)" id="' . $id . '" name="checkboxArray[' . $id . ']" value="' . $id . '" '.$checked.'>';
                        $html .= ' <label class="custom-control-label" for="' . $id . '"></label>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<input type="number" value="' . $checked_val . '" step="0.01" min="0" class="form-control" id="test' . $id . '" onkeypress="return NumberInput(event)" name="check_box_values[' . $id . ']" placeholder="" aria-describedby="checkbox-addon1" ' . $disabled . '>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="col-md-4">';
                        $html .= '<label for="ProfileImage">' .trans("$string_file.please") . " " . trans("$string_file.enter") . " " . $parameterName . " " . trans("$string_file.number") . ':</label>';
                        $html .= '<div class="form-group">';
                        $html .= '<div class="input-group-prepend">';
                        $html .= ' <input type="number" value="' . $free_val . '" step="0.01" min="0" class="form-control"  id="checkboxFreeArray"  name="checkboxFreeArray[' . $id . ']"  placeholder="">';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        break;
                    case "14":
                        $html .= '<div class="row" style="margin-bottom: 10px">';
                        $html .= '<div class="col-md-4">';
                        $html .= '<label for="ProfileImage">' . $parameterName . ':</label>';
                        $html .= '<div class="input-group">';
                        $html .= '<div class="input-group-prepend">';
                        $html .= '<div class="input-group-text">';
                        $html .= '<div class="custom-control custom-checkbox">';
                        $html .= '<input type="checkbox" class="custom-control-input" onclick="invisibleInput(this.value)" id="' . $id . '" name="checkboxArray[' . $id . ']" value="' . $id . '" '.$checked.'>';
                        $html .= ' <label class="custom-control-label" for="' . $id . '"></label>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<input type="number" value="' . $checked_val . '" step="0.01" min="0" class="form-control" id="test' . $id . '" onkeypress="return NumberInput(event)" name="check_box_values[' . $id . ']" placeholder="Enter AC Charges" aria-describedby="checkbox-addon1" ' . $disabled . '>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="col-md-4">';
                        $html .= '<label for="ProfileImage">' . trans("$string_file.charges") .' '.trans("$string_file.type") .':</label>';
                        $html .= '<div class="form-group">';
                        $html .= ' <select  class="form-control"id="checkboxFreeArray"  name="checkboxFreeArray[' . $id . ']"  ><option value="1">' . trans($string_file.'.nominal') . '</option><option value="2">' . trans("$string_file.per_km") . '</option></select>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        break;
                    case "15":
                        $html .= '<div class="row" style="margin-bottom: 10px">';
                        $html .= '<div class="col-md-4">';
                        $html .= '<label for="ProfileImage">' . $parameterName . ':</label>';
                        $html .= '<div class="input-group">';
                        $html .= '<div class="input-group-prepend">';
                        $html .= '<div class="input-group-text">';
                        $html .= '<div class="custom-control custom-checkbox">';
                        $html .= '<input type="checkbox" class="custom-control-input" onclick="invisibleInput(this.value)" id="' . $id . '" name="checkboxArray[' . $id . ']" value="' . $id . '" '.$checked.'>';
                        $html .= ' <label class="custom-control-label" for="' . $id . '"></label>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<input type="number" value="' . $checked_val . '" step="0.01" min="0" class="form-control" id="test' . $id . '" onkeypress="return NumberInput(event)" name="check_box_values[' . $id . ']" placeholder="" aria-describedby="checkbox-addon1" ' . $disabled . '>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="col-md-4">';
                        $html .= '<label for="ProfileImage">' . trans("$string_file.maximum") .' '.trans("$string_file.distance") . ':</label>';
                        $html .= '<div class="form-group">';
                        $html .= '<div class="input-group-prepend">';
                        $html .= ' <input type="number" min="0" class="form-control"  id="checkboxFreeArray"  name="checkboxFreeArray[' . $id . ']"  placeholder="">';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        break;
                    default:
                        $html .= '<div class="row" style="margin-bottom: 10px">';
                        $html .= '<div class="col-md-4">';
                        $html .= '<label for="ProfileImage">' . $parameterName . ':</label>';
                        $html .= '<div class="input-group">';
                        $html .= '<div class="input-group-prepend">';
                        $html .= '<div class="input-group-text">';
                        $html .= '<div class="custom-control custom-checkbox">';
                        $html .= '<input type="checkbox" class="custom-control-input" onclick="invisibleInput(this.value)" id="' . $id . '" name="checkboxArray[' . $id . ']" value="' . $id . '" '.$checked.'>';
                        $html .= ' <label class="custom-control-label" for="' . $id . '"></label>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<input type="number" value="' . $checked_val . '" step="0.01" min="0" class="form-control" id="test' . $id . '" onkeypress="return NumberInput(event)" name="check_box_values[' . $id . ']" placeholder="" aria-describedby="checkbox-addon1" ' . $disabled . '>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                }
            }
        }
        return $html;
    }

    // change status of price card
    public function ChangeStatus($id, $status)
    {
        $validator = Validator::make(
            ['id' => $id, 'status' => $status,],
            ['id' => ['required'], 'status' => ['required', 'integer', 'between:1,2'],]);
        if ($validator->fails()) {
            return redirect()->back();
        }
        $price_card = PriceCard::findOrFail($id);
        $string_file = $this->getStringFile(NULL,$price_card->Merchant);
        if($price_card->Merchant->demo == 1)
        {
            return redirect()->back()->withErrors(trans("$string_file.demo_warning_message"));
        }

        $price_card->status = $status;
        $price_card->save();

        return redirect()->back()->withSuccess(trans("$string_file.status_updated"));
    }

    public function addVariableAndFixed($merchant_id, $vehicle_type_id, $request)
    {
        $newArray = PriceCard::create([
            'base_fare' => $request->base_fare,
            'price_card_name' => $request->price_card_name,
            'free_distance' => $request->free_distance,
            'free_time' => $request->free_time,
            'merchant_id' => $merchant_id,
            'rate_card_scope' => $request->rate_card_scope,
            'country_area_id' => $request->area,
            'service_type_id' => $request->service,
            'delivery_type_id' => $request->delivery_type,
            'vehicle_type_id' => $vehicle_type_id,
            'pricing_type' => $request->price_type,
            'extra_sheet_charge' => $request->extra_sheet_charge,
            'package_id' => $request->package_id,
            'cancel_charges' => $request->cancel_charges,
            'cancel_time' => $request->cancel_time,
            'cancel_amount' => $request->cancel_amount,
//            'driver_cash_booking_limit' => $request->driver_cash_booking_limit,
            'insurnce_enable' => $request->insurnce_enable,
            'insurnce_value' => $request->insurnce_value,
            'insurnce_type' => $request->insurnce_type,
//            'outstation_max_distance' => $request->max_distance,
            'minimum_wallet_amount' => $request->minimum_wallet_amount,
        ]);
        if (isset($request->week_days) && is_array($request->week_days) && count($request->week_days) > 0) {
            $this->AddExtraCharges($request, $newArray->id);
        }
        $this->AddPriceInputValue($request->check_box_values, $request->checkboxArray, $request->checkboxFreeArray, $newArray->id);
        // $newArray->PaymentMethod()->sync($request->input('payment_method'));
        $commission_data = (object)array(
            'commission_type' => $request->commission_type,
            'commission_method' => $request->commission_method,
            'flat_commission' => $request->flat_commission,
            'percentage_commission' => $request->percentage_commission,
            'taxi_commission_type' => $request->taxi_commission_type,
            'taxi_commission_method' => $request->taxi_commission_method,
            'taxi_flat_commission' => $request->taxi_flat_commission,
            'taxi_percentage_commission' => $request->taxi_percentage_commission,
            'hotel_commission_type' => $request->hotel_commission_type,
            'hotel_commission_method' => $request->hotel_commission_method,
            'hotel_flat_commission' => $request->hotel_flat_commission,
            'hotel_percentage_commission' => $request->hotel_percentage_commission,
        );
        $this->AddCommission($newArray->id, $commission_data);
    }

    public function SurgeCharge()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $pricecards = PriceCard::where([['merchant_id', '=', $merchant_id], ['sub_charge_status', '!=', null]])->whereHas('CountryArea',function($q) use($permission_area_ids){
            if(!empty($permission_area_ids)){
                $q->whereIn("id",$permission_area_ids);
            }
        })->latest()->paginate(15);
        $merchant = Merchant::with('PaymentMethod', 'RateCard')->find($merchant_id);
        $config = $merchant->ApplicationConfiguration;
        return view('merchant.pricecard.surgecharge', compact('pricecards', 'config'));
    }

    public function SurgeChargeUpdate(Request $request, $id)
    {
        $status = $request->status;
        $pricecard = PriceCard::findOrFail($id);
        $pricecard->sub_charge_status = $status;
        $pricecard->save();
        $string_file = $this->getStringFile(NULL,$pricecard->Merchant);
//        $key = $status == 1 ? trans('admin.surgechargeon') : trans('admin.surgechargeoff');
        return redirect()->back()->withSuccess(trans("$string_file.status_updated"));
    }

    public function SurgeChargeValUpdate(Request $request)
    {
        $id = $request->docId;
        $pricecard = PriceCard::findOrFail($id);
        $pricecard->sub_charge_type = $request->sub_charge_type;
        $pricecard->sub_charge_value = $request->sub_charge_value;
        $pricecard->save();
        $string_file = $this->getStringFile(NULL,$pricecard->Merchant);
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function NewField(Request $request)
    {
        $priceCardId = 1049;
        $priceCard = PriceCard::find($priceCardId);
        return array_pluck($priceCard->PriceCardValues, 'pricing_parameter_id');
    }

    /**End price card functionality of Taxi Related segment */


    /***********************************************************************/

    /** Start price card functionality of Food & Grocery Related segment */

    public function indexFoodGrocery(Request $request, $price_card_for = null)
    {
//        p($pricer_card_for);
//        $permission_text = $price_card_for == 1 ? 'view_driver_price_card' : 'view_user_price_card';
        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
        $checkPermission = check_permission(1, $all_food_grocery_clone,true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $permission_segments = get_permission_segments(1,true);
        $merchant_id = get_merchant_id();
        $arr_price_card = PriceCard::with('ServiceType', 'CountryArea', 'Segment', 'PriceCardDetail')
            ->whereHas('Segment', function ($q) use($permission_segments){
                $q->where('sub_group_for_admin', 2);
                $q->whereIn('slag',$permission_segments);
//                $q->whereIn('slag', ['FOOD', 'GROCERY','PHARMACY','WATER_TANK_DELIVERY','GAS_DELIVERY']);
            })
            ->where(function ($q) use ($price_card_for) {
                if (!empty($price_card_for)) {
                    $q->where('price_card_for', $price_card_for);
                }
            })
            ->whereHas('CountryArea',function($q) use($permission_area_ids){
                if(!empty($permission_area_ids)){
                    $q->whereIn("id",$permission_area_ids);
                }
            })
            ->where([['merchant_id', '=', $merchant_id]])->paginate(25);
//        $arr_price_type = get_price_card_type();
        if($price_card_for == 1){
            $info_setting = InfoSetting::where('slug','DELIVERY_DRIVER_SERVICE_PRICE_CARD')->first();
        }else{
            $info_setting = InfoSetting::where('slug','DELIVERY_USER_SERVICE_PRICE_CARD')->first();
        }
        return view('merchant.food-grocery-pricecard.index', compact('arr_price_card', 'price_card_for','info_setting'));
    }

    public function addFoodGrocery(Request $request, $price_card_for, $id = null)
    {
        //        $permission_text = $price_card_for == 1 ? 'view_driver_price_card' : 'view_user_price_card';
        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
        $checkPermission = check_permission(1, $all_food_grocery_clone,true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $data = [];
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $price_card = '';
        $arr_services = [];
        $segment_group_id = 1;
        $area_id = NULL;
        $is_demo = false;
        $slab_count = 1;
        if (!empty($id)) {
            $price_card = PriceCard::findorfail($id);
            $pre_title = trans("$string_file.edit");
            $submit_button = trans("$string_file.update");
            $arr_services = $this->getMerchantServicesByArea($price_card->country_area_id, $price_card->segment_id, '', 'array', $segment_group_id);
            $area_id = $price_card->country_area_id;
            $slab_count = $price_card->PriceCardDetail->count();
            $is_demo =  ($merchant->demo == 1 && $price_card->country_area_id == 3) ? true : false;
        } else {
            $pre_title = trans("$string_file.add");
            $submit_button = trans("$string_file.save");
        }
        $title = $pre_title.' '.trans("$string_file.price_card");
        $areas = $this->getMerchantCountryArea($this->getAreaList(false, false)->get());
        $ajax = new AjaxController;
        $sub_group_for_admin = 2; // means food grocery and its cloned
        $request->request->add(['area_id' => $area_id, 'segment_group_id' => $segment_group_id,'sub_group_for_admin'=>$sub_group_for_admin]);
        $arr_segment = $ajax->getCountryAreaSegment($request, 'dropdown');
        $configuration = $merchant->Configuration;
        $step_value = $this->stepValue($merchant->id);
        $data = [
            'price_card' => $price_card,
            'title' => $title,
            'submit_button' => $submit_button,
            'arr_areas' => $areas,
            'arr_services' => $arr_services,
            'arr_segment' => $arr_segment,
            'arr_status' => get_active_status("web",$string_file),
            'condition' => add_blank_option(\Config::get('custom.condition'), trans("$string_file.select")),
            'step_value'=>$step_value
        ];
        if($price_card_for == 1){
            $info_setting = InfoSetting::where('slug','DELIVERY_DRIVER_SERVICE_PRICE_CARD')->first();
        }else{
            $info_setting = InfoSetting::where('slug','DELIVERY_USER_SERVICE_PRICE_CARD')->first();
        }
        return view('merchant.food-grocery-pricecard.form', compact('merchant', 'data', 'slab_count', 'price_card_for','configuration','info_setting','is_demo'));
    }

    public function saveFoodGrocery(Request $request, $id = NULL)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $segment_id = $request->segment_id;
        $country_area_id = $request->country_area_id;
        $price_card_for = $request->price_card_for;
        $validator = Validator::make($request->all(), [
            'service_type_id' => 'required|unique:price_cards,service_type_id,' . $id . ',id,merchant_id,' . $merchant_id . ',segment_id,' . $segment_id . ',country_area_id,' . $country_area_id . ',price_card_for,' . $price_card_for,
            'country_area_id' => 'required|integer',
//            'amount' => 'required',
            'status' => 'required',
            'price_card_for' => 'required',
            'pick_up_fee' => 'required_if:price_card_for,==,1',
            'drop_off_fee' => 'required_if:price_card_for,==,1',
            'condition' => 'required_if:price_card_for,==,2',
            'cart_amount' => 'required_if:price_card_for,==,2',
            'distance_from' => 'required',
            'distance_to' => 'required',
            'slab_amount' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
//            p($errors);
            return redirect()->back()->withInput($request->input())->withErrors($errors[0]);
        }
        DB::beginTransaction();
        try {
            if (!empty($id)) {
                $price_card = PriceCard::Find($id);
            } else {
                $price_card = new PriceCard;
            }

            $time_charges_details = NULL;
            $configuration = Configuration::where('merchant_id',$merchant_id)->first();
            if(isset($configuration->user_time_charges) && $configuration->user_time_charges == 1){
                $time_charges_details = array(
                    "time_from" => $request->time_from,
                    "time_to" => $request->time_to,
                    "charges_type" => $request->charges_type,
                    "charges" => $request->charges,
                    "charge_parameter" => $request->charge_parameter,
                );
                $time_charges_details = json_encode($time_charges_details);
            }

            $price_card->merchant_id = $merchant_id;
            $price_card->segment_id = $segment_id;
            $price_card->country_area_id = $country_area_id;
            $price_card->service_type_id = $request->service_type_id;
            $price_card->pick_up_fee = $request->pick_up_fee;
            $price_card->drop_off_fee = $request->drop_off_fee;
            $price_card->price_card_for = $request->price_card_for;
            $price_card->status = $request->status;
            $price_card->tax = $request->tax;
            $price_card->time_charges_details = $time_charges_details;
            $price_card->cancel_charges = $request->cancel_charges;
            $price_card->cancel_time = ($request->cancel_charges == 1) ? $request->cancel_time : NULL;
            $price_card->cancel_amount = ($request->cancel_charges == 1) ? $request->cancel_amount : NULL;
            $price_card->save();


            $arr_slab = $request->slab_amount;
            $from = $request->distance_from;
            $to = $request->distance_to;
            $condition = $request->condition;
            $cart_amount = $request->cart_amount;
            $detail_status = $request->detail_status;
            $price_card_detail_id = $request->price_card_detail_id;

            foreach ($arr_slab as $key => $slab) {
                if ((isset($from[$key]) && $from[$key] != NULL) && (isset($to[$key]) && $to[$key] != NULL) && (isset($slab) && $slab != NULL)) {
                    if (!empty($price_card_detail_id[$key])) {
                        $detail = PriceCardDetail::Find($price_card_detail_id[$key]);
                    } else {
                        $detail = new PriceCardDetail;
                    }
//
                    $detail->price_card_id = $price_card->id;
                    $detail->distance_from = $from[$key];
                    $detail->distance_to = $to[$key];
                    $detail->condition = isset($condition[$key]) ? $condition[$key] : NULL;
                    $detail->cart_amount = isset($cart_amount[$key]) ? $cart_amount[$key] : NULL;
                    $detail->status = isset($detail_status[$key]) ? $detail_status[$key] : 1;
                    $detail->slab_amount = $slab;
                    $detail->save();

                }else{
                    if (!empty($price_card_detail_id[$key])) {
                        $detail = PriceCardDetail::Find($price_card_detail_id[$key]);
                        $detail->delete();
                    }
                }
            }
//            p($arr_slab);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
            return redirect()->back()->withErrors($message);
        }
        DB::commit();
        return redirect()->route('food-grocery.price_card', [$price_card_for])->withSuccess(trans("$string_file.price_card_added_successfully"));
    }

    public function show($id)
    {
        $merchant_id = get_merchant_id();
        $pricecard = PriceCard::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $config = ApplicationConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        $configuration = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        return view('merchant.pricecard.show', compact('pricecard', 'config', 'configuration'));
    }

    // $check_for  : USER/DRIVER
    public function checkFoodGroceryPriceCard($check_for,$merchant_id, $segment_id, $country_area_id, $service_type_id){
        $price_card_for = ($check_for == "USER") ? 2 : 1;
        $driver_price_card = PriceCard::with('ServiceType', 'CountryArea', 'Segment', 'PriceCardDetail')
            ->whereHas('Segment', function ($q) use($segment_id){
                $q->where('sub_group_for_admin', 2);
                $q->where('id',$segment_id);
            })
            ->whereHas('CountryArea', function ($q) use($country_area_id){
                $q->where('id',$country_area_id);
            })
            ->whereHas('ServiceType', function ($q) use($service_type_id){
                $q->where('id',$service_type_id);
            })
            ->where('price_card_for', $price_card_for)
            ->where([['merchant_id', '=', $merchant_id]])->first();
        if(!empty($driver_price_card)){
            return true;
        }else{
            return false;
        }
    }
}
