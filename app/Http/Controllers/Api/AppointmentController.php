<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointment = Appointment::with(['doctor.user', 'patient', 'schedule'])->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $appointment,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AppointmentRequest $request)
    {
        $validated = $request->validated();
        $appointment = Appointment::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'doctor_shedule_id' => $validated['doctor_shedule_id'] ?? null,
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'status' => $validated['status'] ?? 'pending',

        ]);

        return response()->json([
            'status' => true,
            'message' => 'Appointment booked successfully',
            'data' => $appointment->load(['patient', 'doctor.user']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $appointment = Appointment::with(['doctor.user', 'patient'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $appointment,
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AppointmentRequest $request, string $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Appointment updated successfully',
            'data' => $appointment->load(['patient', 'doctor.user']),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Appointment deleted successfully',
        ], 200);
    }
}
