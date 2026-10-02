<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\Appointment;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Review::with('patient');

        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $reviews = $query->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $reviews,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReviewRequest $request)
    {
        $validated = $request->validated();
        $appointment = Appointment::with('patient')->findOrFail($validated['appointment_id']);

        // Verify the appointment belongs to the logged-in user
        if ($appointment->patient->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to review this appointment.',
            ], 403);
        }

        // Only completed appointments can be reviewed
        if ($appointment->status !== 'confirmed') {
            return response()->json([
                'status' => false,
                'message' => 'You can only review confirmed appointments.',
            ], 422);
        }

        // Prevent duplicate reviews (unique constraint also protects this)
        if ($appointment->review()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'You have already reviewed this appointment.',
            ], 422);
        }

        $review = Review::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'appointment_id' => $validated['appointment_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Review submitted successfully',
            'data' => $review->load('patient'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json([
            'status' => true,
            'message' => 'Review deleted successfully',
        ], 200);
    }
}
