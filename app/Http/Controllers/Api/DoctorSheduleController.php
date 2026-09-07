<?php

namespace App\Http\Controllers\APi;

use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorSheduleRequest;
use App\Models\DoctorSchedule;

class DoctorScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shedule = DoctorSchedule::with('doctor')->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $shedule,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DoctorSheduleRequest $request)
    {
        $shedule = DoctorShedule::create([
            'doctor_id' => $request->doctor_id,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => $request->is_available ?? true,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Doctor schedule created successfully',
            'data' => $shedule,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shedule = DoctorShedule::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $shedule,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DoctorSheduleRequest $request, string $id)
    {
        $schedule = DoctorShedule::findOrFail($id);
        $schedule->update([
            'doctor_id' => $request->doctor_id,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => $request->is_available ?? true,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Doctor schedule updated successfully',
            'data' => $schedule,
        ], 201);
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
