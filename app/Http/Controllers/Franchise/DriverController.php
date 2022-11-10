<?php

namespace App\Http\Controllers\Franchise;


use App\Models\CountryArea;
use App\Models\DriverDocument;
use App\Models\DriverVehicle;
use App\Models\DriverVehicleDocument;
use App\Models\VehicleMake;
use App\Models\VehicleType;
use Auth;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use DB;
use App\Traits\ImageTrait;

class DriverController extends Controller
{
    use ImageTrait;
    public function index()
    {
        $franchise = Auth::user('franchise');
        $id = $franchise->id;
        $drivers = Driver::WhereHas('Franchisee', function ($query) use ($id) {
            $query->where('franchisee_id', $id);
        })->latest()->paginate(25);
        return view('franchise.driver.index', compact('drivers'));
    }

    public function create()
    {
        $area = Auth::user('franchise')->CountryArea;
        return view('franchise.driver.create', compact('area'));
    }

    public function store(Request $request)
    {
        $merchant_id = Auth::user('franchise')->merchant_id;
        $request->validate([
            'fullname' => 'required',
            'email' => 'required|email',
            'phone' => ['required',
                Rule::unique('drivers', 'phoneNumber')->where(function ($query) use ($merchant_id) {
                    return $query->where('merchant_id', $merchant_id);
                })],
            'password' => 'required|confirmed',
            'area' => 'required|exists:country_areas,id',
            'image' => 'required|file'
        ]);

        $driver = Driver::create([
            'merchant_id' => $merchant_id,
            'fullName' => $request->fullname,
            'email' => $request->email,
            'phoneNumber' => $request->phone,
            'country_area_id' => $request->area,
            'password' => Hash::make($request->password),
            'profile_image' => $this->uploadImage('image','driver',$merchant_id)
        ]);
        $driver->Franchisee()->sync(Auth::user('franchise')->id);
        return redirect()->route('franchise.driver.document.show', [$driver->id]);
    }


    public function ShowDocument($id)
    {
        $merchant_id = Auth::user('franchise')->merchant_id;
        $driver = Driver::where([['merchant_id', '=', $merchant_id]])->find($id)->toArray();
        $areas = CountryArea::with('Documents')->where('id', '=', $driver['country_area_id'])->first();
        return view('franchise.driver.create_document', compact('areas', 'id'));
    }

    public function StoreDocument(Request $request, $id)
    {
        $merchant_id = Auth::user('franchise')->merchant_id;
        $request->validate([
            'document' => 'required'
        ]);
        $driver = Driver::where([['merchant_id', '=', $merchant_id]])->find($id);
        $images = $request->file('document');
        $auto_verify = $request->auto_verify;
        $status = $auto_verify == "" ? 1 : $auto_verify;
        foreach ($images as $key => $image) {
            $document_id = $key;

            DriverDocument::create([
                'driver_id' => $id,
                'document_id' => $document_id,
                'document_file' => $this->uploadImage($image,'driver',$merchant_id,'multiple'),
                'document_verification_status' => $status,
            ]);
        }
        $driver->signupStep = 1;
        $driver->save();
        return redirect()->route('franchise.driver.vehicle.create', [$id]);
    }


    public function CreateVehicle($id)
    {
        $merchant_id = Auth::user('franchise')->merchant_id;
        $driver = Driver::where([['merchant_id', '=', $merchant_id]])->find($id);
        $vehicletypes = VehicleType::where([['merchant_id', '=', $merchant_id]])->get();
        $vehiclemakes = VehicleMake::where([['merchant_id', '=', $merchant_id]])->get();
        $docs = CountryArea::with('VehicleDocuments')->where('id', $driver->country_area_id)->first();
        return view('franchise.driver.create_vehicle', compact('driver', 'vehicletypes', 'vehiclemakes', 'docs'));
    }

    public function StoreVehicle(Request $request, $id)
    {
        $request->validate([
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'vehicle_make_id' => 'required|exists:vehicle_makes,id',
            'vehicle_model_id' => 'required|exists:vehicle_models,id',
            'vehicle_number' => 'required',
            'vehicle_color' => 'required',
            'document' => 'required',
            'car_number_plate_image' => 'required',
            'car_image' => 'required',
            'services' => 'required'
        ]);
        $merchant_id = Auth::user('franchise')->merchant_id;
        $image = $this->uploadImage('car_image','vehicle_document',$merchant_id);
        $image1 = $this->uploadImage('car_number_plate_image','vehicle_document',$merchant_id);
        $auto_verify = $request->auto_verify;
        $status = $auto_verify == "" ? 1 : $auto_verify;
        if ($status == 1) {
            $vehicle_active_status = 2;
        } else {
            $vehicle_active_status = 1;
        }
        $vehicle = DriverVehicle::create([
            'driver_id' => $id,
            'vehicle_type_id' => $request->vehicle_type_id,
            'vehicle_make_id' => $request->vehicle_make_id,
            'vehicle_model_id' => $request->vehicle_model_id,
            'vehicle_number' => $request->vehicle_number,
            'vehicle_color' => $request->vehicle_color,
            'vehicle_image' => $image,
            'vehicle_number_plate_image' => $image1,
            'vehicle_active_status' => $vehicle_active_status,
            'vehicle_verification_status' => $status
        ]);
        $vehicle->ServiceTypes()->sync($request->services);
        $images = $request->file('document');
        foreach ($images as $key => $image) {
            $document_id = $key;
            DriverVehicleDocument::create([
                'driver_vehicle_id' => $vehicle->id,
                'document_id' => $document_id,
                'document' => $this->uploadImage($image,'driver',$merchant_id,'multiple'),
                'document_verification_status' => $status,
            ]);
        }
        $driver = Driver::where([['merchant_id', '=', $merchant_id]])->find($id);
        if ($status == 1) {
            $driver->signupStep = 2;
        } else {
            $driver->signupStep = 3;
        }
        $driver->save();
        return redirect()->route('franchise-driver.index');
    }


    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
