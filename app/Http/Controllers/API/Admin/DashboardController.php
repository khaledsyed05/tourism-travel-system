<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\TourPackage;
use App\Models\Destination;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function summary()
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        $summary = [
            'total_users' => User::count(),
            'new_users_this_month' => User::where('created_at', '>=', $lastMonth)->count(),
            'total_bookings' => Booking::count(),
            'new_bookings_this_month' => Booking::where('created_at', '>=', $lastMonth)->count(),
            'total_revenue' => Booking::sum('total_price'),
            'revenue_this_month' => Booking::where('created_at', '>=', $lastMonth)->sum('total_price'),
            'total_tour_packages' => TourPackage::count(),
            'total_destinations' => Destination::count(),
            'recent_bookings' => Booking::with('user', 'tourPackage')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
            'popular_destinations' => Destination::withCount('tourpackages')
                ->orderBy('tourpackages_count', 'desc')
                ->take(5)
                ->get(),
        ];

        return response()->json($summary);
    }
}
