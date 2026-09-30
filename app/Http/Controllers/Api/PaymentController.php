<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with(['appointment.doctor.user', 'patient'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $payments,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequest $request)
    {
        $validated = $request->validated();

        $payment = Payment::create([
            'appointment_id' => $validated['appointment_id'],
            'patient_id' => $validated['patient_id'],
            'amount' => $validated['amount'],
            'status' => $validated['status'] ?? 'paid',
            'paid_at' => $validated['paid_at'] ?? now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Payment recorded successfully',
            'data' => $payment->load(['appointment', 'patient']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::with(['appointment.doctor.user', 'patient'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $payment,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentRequest $request, string $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Payment updated successfully',
            'data' => $payment->load(['appointment', 'patient']),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Payment deleted successfully',
        ], 200);
    }
}
