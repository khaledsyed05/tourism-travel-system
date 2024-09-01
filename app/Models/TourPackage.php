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
        'max_participants', 'published', 'destination_id', 'itinerary', 'pricing_tiers'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'itinerary' => 'array',
        'pricing_tiers' => 'array',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
