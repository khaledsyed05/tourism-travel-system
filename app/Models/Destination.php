<?php

namespace App\Models;

use App\Traits\Toggleable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Destination extends Model
{
    use HasFactory, Toggleable;

    protected $fillable = [
        'name',
        'description',
        'country',
        'city',
        'published',
    ];

    public function tourpackages()
    {
        return $this->hasMany(TourPackage::class);
    }
}
