<?php

namespace App\Models\DriverAgency;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
use App;
use App\Models\Merchant;
use App\Models\Driver;
use App\Models\Country;
use App\Models\AccountType;
use App\Models\BusinessSegment;

class DriverAgency extends Authenticatable
{
    use Notifiable;
	use SoftDeletes;
	
	protected $guarded = [];
	
	protected $hidden = [
        'password', 'remember_token','pivot',
    ];
	
	public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function Country()
    {
        return $this->belongsTo(Country::class);
    }

    public function Driver()
    {
        return $this->hasMany(Driver::class)->where('driver_delete', '=', NULL);
    }

    public function AccountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    public function BusinessSegment(){
        return $this->belongsToMany(BusinessSegment::class,'business_segment_driver_agency','driver_agency_id');
    }
}
