<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRatingRequest;
use App\Http\Resources\RatingResource;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Store or update a newly created rating in storage.
     */
    public function store(StoreRatingRequest $request)
    {
        $data = $request->validated();
        
        $rating = Rating::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'guide_id' => $data['guide_id']
            ],
            [
                'score' => $data['score'],
                'comment' => $data['comment'] ?? null
            ]
        );

        // Optionally eager load the user for the resource
        $rating->load('user');

        return new RatingResource($rating);
    }

    /**
     * Remove the specified rating from storage.
     */
    public function destroy(Rating $rating, Request $request)
    {
        if ($rating->user_id !== $request->user()->id && $request->user()->id !== 1) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $rating->delete();
        return response()->json(null, 204);
    }
}
