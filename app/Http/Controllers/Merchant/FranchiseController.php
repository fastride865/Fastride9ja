<?php

namespace App\Http\Controllers\Merchant;


use App\Models\Country;
use App\Traits\AreaTrait;
use Auth;
use App\Models\Franchisee;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\FranchiseeRequest;
use Illuminate\Support\Facades\Hash;

class FranchiseController extends Controller
{
    use AreaTrait;

    public function index()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $franchisees = Franchisee::where([['merchant_id', '=', $merchant_id]])->latest()->paginate(25);
        return view('merchant.franchise.index', compact('franchisees'));
    }

    public function create()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $area = $this->getAreaList(false);
        $areas = $area->get();
        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get();
        return view('merchant.franchise.create', compact('countries', 'areas'));
    }

    public function store(FranchiseeRequest $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        Franchisee::create([
            'merchant_id' => $merchant_id,
            'name' => $request->name,
            'country_area_id' => $request->area,
            'alias' => $request->alias,
            'email' => $request->email,
            'phone' => $request->country . $request->phone,
            'contact_person_name' => $request->contact,
            'commission_percentage' => $request->commission,
            'password' => Hash::make($request->password),
        ]);
        return redirect()->back()->with('message566', trans('admin.message566'));
    }

    public function edit($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $franchisee = Franchisee::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        return view('merchant.franchise.edit', compact('franchisee'));
    }

    public function update(Request $request, $id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'name' => "required",
            'email' => ['required', 'email', 'max:255',
                Rule::unique('users', 'email')->where(function ($query) use ($merchant_id) {
                    $query->where([['merchant_id', '=', $merchant_id]]);
                })->ignore($id)],
            'phone' => 'required',
            'contact' => 'required',
            'commission' => 'required|integer',
            'password' => 'required_if:edit_password,1'
        ]);
        $franchisee = Franchisee::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $franchisee->name = $request->name;
        $franchisee->phone = $request->phone;
        $franchisee->email = $request->email;
        $franchisee->contact_person_name = $request->contact;
        $franchisee->commission_percentage = $request->commission;
        if ($request->edit_password == 1) {
            $password = Hash::make($request->password);
            $franchisee->password = $password;
        }
        $franchisee->save();
        return redirect()->back()->with('message567', trans('admin.message567'));
    }

    public function ChangeStatus($id, $status)
    {
        $validator = Validator::make(
            [
                'id' => $id,
                'status' => $status,
            ],
            [
                'id' => ['required'],
                'status' => ['required', 'integer', 'between:1,2'],
            ]);
        if ($validator->fails()) {
            return redirect()->back();
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $user = Franchisee::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $user->status = $status;
        $user->save();
        return redirect()->route('franchisee.index');
    }
}
