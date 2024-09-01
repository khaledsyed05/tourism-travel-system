<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TogglableController;
use App\Http\Requests\TourPackageRequest;
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
        $keyword = $request->input('query', ''); // Provide a default empty string
        $minDuration = $request->input('min_duration');
        $maxDuration = $request->input('max_duration');

        $results = $this->tourPackageService->searchTourPackages($keyword, $minDuration, $maxDuration);

        return response()->json($results);
    }

    public function togglePublished(TourPackage $tourPackage)
    {
        return $this->toggle($tourPackage, 'published');
    }
    
}
