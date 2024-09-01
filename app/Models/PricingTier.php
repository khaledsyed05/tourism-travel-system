<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingTier extends Model
{
    use HasFactory;
    protected $fillable = [ 'name', 'price', 'description','tour_package_id'];

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }
}
