<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Review;
use Carbon\Carbon;

class ReviewService
{
    public function createReview($userId, $tourPackageId, $bookingId, $rating, $comment)
    {
        $booking = Booking::findOrFail($bookingId);

        // Check if the booking belongs to the user and for the specified tour package
        if ($booking->user_id !== $userId || $booking->tour_package_id !== $tourPackageId) {
            throw new \Exception('Invalid booking for this user and tour package.');
        }

        // Check if the tour has ended
        if (Carbon::now()->lt($booking->tourPackage->start_date)) {
            throw new \Exception('Cannot review a tour that has not started yet.');
        }

        // Check if payment has been made
        if (!$booking->payments()->where('status', 'completed')->exists()) {
            throw new \Exception('Cannot review a tour that has not been paid for.');
        }

        // Create the review
        return Review::create([
            'user_id' => $userId,
            'tour_package_id' => $tourPackageId,
            'booking_id' => $bookingId,
            'rating' => $rating,
            'comment' => $comment,
        ]);
    }
}