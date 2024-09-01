<?php

namespace App\Services;

use App\Models\TourPackage;
use Illuminate\Database\Eloquent\Collection;

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

    public function searchTourPackages(string $keyword = '', ?int $minDuration = null, ?int $maxDuration = null)
    {
        return TourPackage::query()
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            })
            ->when($minDuration !== null, function ($query) use ($minDuration) {
                $query->where('duration_days', '>=', $minDuration);
            })
            ->when($maxDuration !== null, function ($query) use ($maxDuration) {
                $query->where('duration_days', '<=', $maxDuration);
            })
            ->get();
    }
}
