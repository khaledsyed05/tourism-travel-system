<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourPackage;
use App\Models\PricingTier;

class PricingTierController extends Controller
{
    public function index(TourPackage $tourPackage)
    {
        return response()->json($tourPackage->pricingTiers);
    }

    public function store(Request $request, TourPackage $tourPackage)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        $pricingTier = $tourPackage->pricingTiers()->create($validatedData);

        return response()->json($pricingTier, 201);
    }

    public function show(TourPackage $tourPackage, PricingTier $pricingTier)
    {
        // Check if the pricing tier belongs to the tour package
        if ($pricingTier->tour_package_id !== $tourPackage->id) {
            return response()->json(['error' => 'Pricing tier not found for this tour package'], 404);
        }

        return response()->json($pricingTier);
    }

    public function update(Request $request, TourPackage $tourPackage, PricingTier $pricingTier)
    {
        // Check if the pricing tier belongs to the tour package
        if ($pricingTier->tour_package_id !== $tourPackage->id) {
            return response()->json(['error' => 'Pricing tier not found for this tour package'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'description' => 'sometimes|required|string',
        ]);

        $pricingTier->update($validatedData);

        return response()->json($pricingTier);
    }

    public function destroy(TourPackage $tourPackage, PricingTier $pricingTier)
    {
        // Check if the pricing tier belongs to the tour package
        if ($pricingTier->tour_package_id !== $tourPackage->id) {
            return response()->json(['error' => 'Pricing tier not found for this tour package'], 404);
        }

        $pricingTier->delete();

        return response()->json(null, 204);
    }
}
