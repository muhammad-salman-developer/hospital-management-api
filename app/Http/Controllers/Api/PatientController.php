<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PatientRequest;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = Patient::with('user')->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $patients,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PatientRequest $request)
    {
        
        $validated = $request->validated();


        // Agar user_id diya gaya hai (online/registered patient)
        if (! empty($validated['user_id'])) {
            // Sirf apna profile bana sake, ya admin kisi ka bhi bana sake
            if (! $request->user()->hasRole('admin') && $request->user()->id != $validated['user_id']) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to create this profile.',
                ], 403);
            }

            $patient = Patient::create($validated);
        } else {
            // Walk-in patient (koi user_id nahi) - only admin/reception bana sake
            if (! $request->user()->hasRole('admin')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Only admin can add walk-in patients.',
                ], 403);
            }
            // $patient = Patient::updateOrCreate(
            //     ['user_id' => $validated['user_id']],
            //     $validated
            // );
            $patient = Patient::create($validated);
        }

        return response()->json([
            'status' => true,
            'message' => 'Patient profile saved successfully',
            'data' => $patient->load('user'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $patient = Patient::with('user')->findOrFail($id);

        if (! $request->user()->hasRole('admin') && $request->user()->id != $patient->user_id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to view this profile.',
            ], 403);
        }

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

        if (! $request->user()->hasRole('admin') && $request->user()->id != $patient->user_id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to update this profile.',
            ], 403);
        }

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

    public function myProfile(Request $request)
    {
        $patient = Patient::with('user')->where('user_id', $request->user()->id)->first();

        return response()->json([
            'status' => true,
            'data' => $patient,
        ], 200);
    }

    public function myPatients(Request $request)
    {
        $patients = Patient::where('user_id', $request->user()->id)->get();

        return response()->json([
            'status' => true,
            'data' => $patients,
        ], 200);
    }
}
