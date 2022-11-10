<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverVehicle extends Model
{
    protected $guarded = [];

    protected $hidden = ['VehicleType', 'VehicleMake', 'VehicleModel', 'ServiceTypes','Driver','pivot'];

    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function OwnerDriver()
    {
        return $this->belongsTo(Driver::class, 'owner_id');
    }
    
    public function OwnerDetails()
    {
        return $this->hasOne(VehicleOwnerDetail::class, 'driver_vehicle_id');
    }

    public function Drivers()
    {
        return $this->belongsToMany(Driver::class)->withPivot('driver_id','vehicle_active_status');
    }

    public function ServiceTypes()
    {
        return $this->belongsToMany(ServiceType::class)->withPivot('segment_id');
    }

    public function VehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function VehicleMake()
    {
        return $this->belongsTo(VehicleMake::class);
    }

    public function VehicleModel()
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function DriverVehicleDocument()
    {
        return $this->hasMany(DriverVehicleDocument::class);
    }

    public function getDrivers()
    {
        return $this->belongsToMany(Driver::class);
    }
    public function Merchant()
    {
        return $this->belongsTo(Driver::class);
    }

}
