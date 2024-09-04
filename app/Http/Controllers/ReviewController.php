<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReviewRequest;
use App\Http\Requests\ReviewRequest;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function store(ReviewRequest $request)
    {
        try {
            $review = $this->reviewService->createReview(
                $request->user()->id,
                $request->tour_package_id,
                $request->booking_id,
                $request->rating,
                $request->comment
            );

            return response()->json([
                'message' => 'Review created successfully',
                'review' => $review
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create review',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
