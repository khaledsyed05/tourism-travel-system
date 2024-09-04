<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\TourPackage;
use App\Models\Destination;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class AnalyticsController extends Controller
{
    public function overview()
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();
        $lastYear = $now->copy()->subYear();

        $overview = [
            'users' => [
                'total' => User::count(),
                'new_this_month' => User::where('created_at', '>=', $lastMonth)->count(),
                'growth_rate' => $this->calculateGrowthRate(User::class, $lastMonth, $lastYear),
            ],
            'bookings' => [
                'total' => Booking::count(),
                'new_this_month' => Booking::where('created_at', '>=', $lastMonth)->count(),
                'growth_rate' => $this->calculateGrowthRate(Booking::class, $lastMonth, $lastYear),
            ],
            'revenue' => [
                'total' => Booking::sum('total_price'),
                'this_month' => Booking::where('created_at', '>=', $lastMonth)->sum('total_price'),
                'growth_rate' => $this->calculateRevenueGrowthRate($lastMonth, $lastYear),
            ],
            'tour_packages' => [
                'total' => TourPackage::count(),
                'active' => TourPackage::where('published', true)->count(),
            ],
            'destinations' => [
                'total' => Destination::count(),
                'active' => Destination::where('published', true)->count(),
            ],
        ];

        return response()->json($overview);
    }

    public function sales(Request $request)
    {
        $period = $request->input('period', 'month');
        $now = Carbon::now();
        $startDate = $this->getStartDate($period);

        $sales = Booking::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_price) as total')
        )
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return response()->json($sales);
    }

    public function users(Request $request)
    {
        $period = $request->input('period', 'all');
        $now = Carbon::now();
        $startDate = $this->getStartDate($period);
    
        $query = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        );
    
        if ($period !== 'all' && $startDate) {
            $query->where('created_at', '>=', $startDate);
        }
    
        $users = $query->groupBy('date')
            ->orderBy('date')
            ->get();
    
        $diagnostics = [
            'total_users' => User::count(),
            'period' => $period,
            'start_date' => $startDate ? $startDate->toDateTimeString() : 'N/A',
            'now' => $now->toDateTimeString(),
        ];
    
        return response()->json([
            'users' => $users,
            'diagnostics' => $diagnostics
        ]);
    }

    public function popularDestinations()
    {
        $popularDestinations = Destination::withCount('tourpackages')
            ->orderBy('tourpackages_count', 'desc')
            ->take(10)
            ->get();

        return response()->json($popularDestinations);
    }

    public function popularPackages()
    {
        $popularPackages = TourPackage::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->take(10)
            ->get();

        return response()->json($popularPackages);
    }

    private function calculateGrowthRate($model, $lastMonth, $lastYear)
    {
        $thisMonth = $model::where('created_at', '>=', $lastMonth)->count();
        $lastMonthCount = $model::whereBetween('created_at', [$lastYear, $lastMonth])->count();
        
        if ($lastMonthCount == 0) {
            return $thisMonth > 0 ? 100 : 0;
        }
        
        return (($thisMonth - $lastMonthCount) / $lastMonthCount) * 100;
    }

    private function calculateRevenueGrowthRate($lastMonth, $lastYear)
    {
        $thisMonth = Booking::where('created_at', '>=', $lastMonth)->sum('total_price');
        $lastMonthRevenue = Booking::whereBetween('created_at', [$lastYear, $lastMonth])->sum('total_price');
        
        if ($lastMonthRevenue == 0) {
            return $thisMonth > 0 ? 100 : 0;
        }
        
        return (($thisMonth - $lastMonthRevenue) / $lastMonthRevenue) * 100;
    }

    private function getStartDate($period)
    {
        $now = Carbon::now();
        switch ($period) {
            case 'week':
                return $now->startOfWeek();
            case 'month':
                return $now->startOfMonth();
            case 'year':
                return $now->startOfYear();
            default:
                return $now->subDays(30);
        }
    }
}
