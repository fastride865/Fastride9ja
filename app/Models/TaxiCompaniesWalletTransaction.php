<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxiCompaniesWalletTransaction extends Model
{
    protected $guarded = [];
    protected $table = 'taxi_companies_wallet_transaction';

    public function TaxiCompany()
    {
        return $this->belongsTo(TaxiCompany::class);
    }
}
