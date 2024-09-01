<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PricingTier;
use Illuminate\Http\Request;
use App\Http\Requests\BookingRequest;
use App\Models\TourPackage;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with(['user', 'tourPackage'])->get();
        return response()->json($bookings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookingRequest $request)
    {
        $validatedData = $request->validated();

        $pricingTier = PricingTier::findOrFail($validatedData['pricing_tier_id']);

        $booking = new Booking($validatedData);
        $booking->user_id = Auth::id();
        $booking->status = 'pending';
        $booking->total_price = $pricingTier->price * $validatedData['number_of_participants'];
        $booking->save();

        return response()->json($booking, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        return response()->json($booking->load(['user', 'tourPackage']));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(BookingRequest $request, Booking $booking)
    {
        $validatedData = $request->validated();

        $booking->update($validatedData);

        if ($request->has('number_of_participants') || $request->has('pricing_tier_id')) {
            $pricingTier = PricingTier::findOrFail($validatedData['pricing_tier_id']);
            $booking->total_price = $pricingTier->price * $validatedData['number_of_participants'];
            $booking->save();
        }

        return response()->json($booking);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();
        return response()->json(null, 204);
    }

    public function cancel(Booking $booking)
    {
        $booking->status = 'cancelled';
        $booking->save();
        return response()->json($booking);
    }

    private function getPricingTier($tourPackage, $pricingTierId)
    {
        return collect($tourPackage->pricing_tiers)->firstWhere('id', $pricingTierId);
    }
}
