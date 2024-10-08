<?php

namespace App\Models;

use App\Traits\Toggleable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    use HasFactory, Toggleable;
    protected $fillable = [
        'name', 'description', 'duration_days', 'start_date', 'end_date',
        'max_participants', 'published', 'destination_id', 'itinerary', 'featured',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'published' => 'boolean',
        'featured' => 'boolean',
        'itinerary' => 'array',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
    public function pricingTiers()
    {
        return $this->hasMany(PricingTier::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function reviews()
{
    return $this->hasMany(Review::class);
}
}
