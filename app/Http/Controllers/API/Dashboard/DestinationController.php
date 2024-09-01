<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Http\Controllers\TogglableController;
use App\Http\Requests\DestinationRequest;
use App\Models\Destination;
use App\Models\TourPackage;
use App\Traits\Toggleable;

class DestinationController extends TogglableController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $destinations = Destination::whereOn('published')->get();
        return response()->json($destinations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DestinationRequest $request)
    {
        $destination = Destination::create($request->validated());
        return response()->json($destination, 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(Destination $destination)
    {
        return response()->json($destination);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DestinationRequest $request, Destination $destination)
    {
        $validatedData = $request->validated();

        $updated = $destination->update($validatedData);

        if ($updated) {
            return response()->json([
                'message' => 'Destination updated successfully',
                'destination' => $destination->fresh()
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to update destination',
                'destination' => $destination,
                'input' => $validatedData
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Destination $destination)
    {
        $destination->delete();
        return response()->json([
            'message' => 'Destination deleted successfuly'
        ]);
    }
    public function togglePublished(Destination $destination)
    {
        return $this->toggle($destination, 'published');
    }
}
