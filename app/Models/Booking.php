<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'tour_package_id',
        'booking_date',
        'number_of_participants',
        'status',
        'total_price',
        'special_requirements',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
