<?php

namespace App\Http\Controllers\Merchant;

use App\Models\CountryArea;
use Auth;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\RewardPoint;
use App\Models\ApplicationConfiguration;

class RewardController extends Controller
{
    public function index()
    {
        $checkPermission =  check_permission(1,'view_reward');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $app_config = ApplicationConfiguration:: where('merchant_id', $merchant_id)->first();
        if ($app_config->reward_points != 1) {
            die('unauthorized');
        }

        $merchant = Merchant::find($merchant_id);
        $rewards = $merchant->rewardPoints;

        return view('merchant.reward.index', compact('rewards'));
    }

    public function create()
    {
        $checkPermission =  check_permission(1,'create_reward');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;

        $country_areas = CountryArea:: where('merchant_id', $merchant_id)->get();
        return view('merchant.reward.create', compact('country_areas'));
    }

    public function store(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $app_config = ApplicationConfiguration:: where('merchant_id', $merchant_id)->first();
        if ($app_config->reward_points != 1) {
            die('unauthorized');
        }

        $request->validate([
            'user_registration_reward' => 'required|numeric',
            'driver_registration_reward' => 'required|numeric',
            'country_area' => 'required',
            'user_referral_reward' => 'required|numeric',
            'driver_referral_reward' => 'required|numeric',
            'trips_count' => 'required|numeric',
            'max_redeem' => 'required|numeric',
            'value_equals' => 'required|numeric',
        ]);

        // create reward point
        $reward = RewardPoint:: updateOrCreate([
            'merchant_id' => $merchant_id,
        ], [
            'registration_enable' => $request->registration_enable,
            'country_area_id' => $request->country_area,
            'user_registration_reward' => ($request->user_registration_reward) ? $request->user_registration_reward : 0,
            'driver_registration_reward' => ($request->driver_registration_reward) ? $request->driver_registration_reward : 0,
            'referral_enable' => $request->referral_enable,
            'user_referral_reward' => ($request->user_referral_reward) ? $request->user_referral_reward : 0,
            'driver_referral_reward' => ($request->driver_referral_reward) ? $request->driver_referral_reward : 0,
            'trips_count' => $request->trips_count,
            'max_redeem' => $request->max_redeem,
            'value_equals' => $request->value_equals,
            'active' => 1
        ]);

        if ($reward) {
            return redirect()->back()->with('reward', __('admin.reward.added'));
        }

        return redirect()->back()->withInput()->with('reward', __('admin.swr'));
    }

    public function edit($id)
    {
        $checkPermission =  check_permission(1,'edit_reward');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $app_config = ApplicationConfiguration:: where('merchant_id', $merchant_id)->first();
        if ($app_config->reward_points != 1) {
            die('unauthorized');
        }
        $country_areas = CountryArea:: where('merchant_id', $merchant_id)->get();
        $reward = RewardPoint:: where('merchant_id', $merchant_id)->where('id', $id)->first();
        return view('merchant.reward.edit', compact('reward', 'country_areas'));
    }

    public function update(Request $request, $id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $app_config = ApplicationConfiguration:: where('merchant_id', $merchant_id)->first();
        if ($app_config->reward_points != 1) {
            die('unauthorized');
        }

        $request->validate([
            'country_area' => 'required',
            'trips_count' => 'required|numeric',
            'max_redeem' => 'required|numeric',
            'value_equals' => 'required|numeric',
        ]);

        // create reward point
        $reward = RewardPoint:: where('id', $id)->where('merchant_id', $merchant_id)->update([
            'registration_enable' => $request->registration_enable,
            'country_area_id' => $request->country_area,
            'user_registration_reward' => ($request->user_registration_reward) ? $request->user_registration_reward : 0,
            'driver_registration_reward' => ($request->driver_registration_reward) ? $request->driver_registration_reward : 0,
            'referral_enable' => $request->referral_enable,
            'user_referral_reward' => ($request->user_referral_reward) ? $request->user_referral_reward : 0,
            'driver_referral_reward' => ($request->driver_referral_reward) ? $request->driver_referral_reward : 0,
            'trips_count' => $request->trips_count,
            'max_redeem' => $request->max_redeem,
            'value_equals' => $request->value_equals,
            'active' => $request->active,
        ]);

        if ($reward) {
            return redirect()->back()->with('reward', __('admin.reward.updated'));
        }

        return redirect()->back()->withInput()->with('reward', __('admin.swr'));
    }

    public function destroy($id)
    {
        $reward = RewardPoint::find($id);
        if ($reward->delete()) {
            return redirect()->back()->with('reward', __('admin.deleted.successfully'));
        }
        return redirect()->back()->with('reward', __('admin.swr'));
    }


}
