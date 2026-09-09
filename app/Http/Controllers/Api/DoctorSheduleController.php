<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorSheduleRequest;
use App\Models\DoctorShedule;
use Illuminate\Http\Request;

class DoctorSheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DoctorShedule::with('doctor.user');

        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $schedules = $query->get();

        return response()->json([
            'status' => true,
            'data' => $schedules,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * (Agar doctor_id + day ka record already hai to UPDATE karega, warna NAYA banayega)
     */
    public function store(DoctorSheduleRequest $request)
    {
        $validated = $request->validated();
        $validated['is_available'] = $validated['is_available'] ?? true;

        $schedule = DoctorShedule::updateOrCreate(
            [
                'doctor_id' => $validated['doctor_id'],
                'day' => $validated['day'],
            ],
            [
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'is_available' => $validated['is_available'],
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Doctor schedule saved successfully',
            'data' => $schedule->load('doctor'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = DoctorShedule::with('doctor')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $schedule,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DoctorSheduleRequest $request, string $id)
    {
        $schedule = DoctorShedule::findOrFail($id);

        $validated = $request->validated();
        $validated['is_available'] = $validated['is_available'] ?? true;

        $schedule->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Doctor schedule updated successfully',
            'data' => $schedule->load('doctor'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedule = DoctorShedule::findOrFail($id);
        $schedule->delete();

        return response()->json([
            'status' => true,
            'message' => 'Doctor schedule deleted successfully',
        ], 200);
    }
}
