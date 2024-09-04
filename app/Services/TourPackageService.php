<?php

namespace App\Services;

use App\Models\Destination;
use App\Models\TourPackage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class TourPackageService
{
    public function getAllTourPackages(array $filters = [])
    {
        return TourPackage::query()
            ->whereOn('published')
            ->when($filters['destination_id'] ?? null, fn($query, $destinationId) => $query->where('destination_id', $destinationId))
            ->with('destination')->get();
    }

    public function createTourPackage(array $data): TourPackage
    {
        return TourPackage::create($data);
    }

    public function updateTourPackage(TourPackage $tourPackage, array $data): TourPackage
    {
        $tourPackage->update($data);
        return $tourPackage->fresh();
    }

    public function deleteTourPackage(TourPackage $tourPackage): bool
    {
        return $tourPackage->delete();
    }

    public function findTourPackageById(int $id): ?TourPackage
    {
        return TourPackage::findOrFail($id);
    }

    public function searchTourPackages(Request $request)
    {
        $query = TourPackage::query();

        // Keyword search
        if ($request->has('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhereHas('destination', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        // Duration filter
        if ($request->has('min_duration')) {
            $query->where('duration_days', '>=', $request->min_duration);
        }
        if ($request->has('max_duration')) {
            $query->where('duration_days', '<=', $request->max_duration);
        }

        // Destination filter
        if ($request->has('destination')) {
            $destination = Destination::where('name', 'like', "%{$request->destination}%")->first();
            if ($destination) {
                $query->where('destination_id', $destination->id);
            }
        }

        // Date filters
        if ($request->has('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        // Participant filter
        if ($request->has('participants')) {
            $query->where('max_participants', '>=', $request->participants);
        }

        // Published filter
        if ($request->has('published')) {
            $query->where('published', $request->boolean('published'));
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        return $query->paginate($perPage);
    }
}
