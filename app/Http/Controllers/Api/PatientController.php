<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PatientRequest;
use App\Models\Patient;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patient = Patient::with('user')->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $patient,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PatientRequest $request)
    {
        $validated = $request->validated();

        // Sirf apna profile bana sake, ya admin kisi ka bhi bana sake
        if (! $request->user()->hasRole('admin') && $request->user()->id != $validated['user_id']) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to create this profile.',
            ], 403);
        }
        $patient = Patient::updateOrCreate(
            ['user_id' => $validated['user_id']],
            $validated
        );

        return response()->json([
            'status' => true,
            'message' => 'Patient profile created successfully',
            'data' => $patient->load('user'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $patient = Patient::with('user')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $patient,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PatientRequest $request, string $id)
    {
        $patient = Patient::findOrFail($id);

        $patient->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Patient profile updated successfully',
            'data' => $patient->load('user'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();

        return response()->json([
            'status' => true,
            'message' => 'Patient profile deleted successfully',
        ], 200);
    }
}
