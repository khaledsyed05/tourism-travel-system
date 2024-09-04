<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TogglableController;
use App\Http\Requests\TourPackageRequest;
use App\Models\Destination;
use App\Models\TourPackage;
use App\Services\TourPackageService;
use Illuminate\Http\Request;

class TourPackageController extends TogglableController
{
    protected $tourPackageService;

    public function __construct(TourPackageService $tourPackageService)
    {
        $this->tourPackageService = $tourPackageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tourPackages = $this->tourPackageService->getAllTourPackages($request->all());
        return response()->json($tourPackages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TourPackageRequest $request)
    {
        $tourPackage = $this->tourPackageService->createTourPackage($request->validated());
        return response()->json($tourPackage, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $tourPackage = $this->tourPackageService->findTourPackageById($id);
        return response()->json($tourPackage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TourPackageRequest $request, TourPackage $tourPackage)
    {
        $updatedTourPackage = $this->tourPackageService->updateTourPackage($tourPackage, $request->validated());
        return response()->json($updatedTourPackage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TourPackage $tourPackage)
    {
        $this->tourPackageService->deleteTourPackage($tourPackage);
        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        $tourPackages = $this->tourPackageService->searchTourPackages($request);
        return response()->json($tourPackages);
    }

    public function togglePublished(TourPackage $tourPackage)
    {
        $wasPublished = $tourPackage->published;
        $result = $this->toggle($tourPackage, 'published');
    
        // If the package was unpublished, also unfeature it
        if ($wasPublished && !$tourPackage->published) {
            $tourPackage->featured = false;
            $tourPackage->save();
        }
        return $result;
    }
    public function toggleFeatured(TourPackage $tourPackage)
    {
        // Only allow featuring if the tour package is published
        if (!$tourPackage->published) {
            return response()->json(['message' => 'Cannot feature an unpublished tour package'], 400);
        }
        return $this->toggle($tourPackage, 'featured');
    }
}
