<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorRequest;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = Doctor::with(['user', 'department'])->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $doctors,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DoctorRequest $request)
    {
        $validated = $request->validated();

        $doctor = DB::transaction(function () use ($validated, $request) {

            // Step 1: User banao
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignRole('doctor');
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('doctors', 'public');
            }
            // Step 2: Doctor banao, ABHI BANE HUE user ki id se link kar k
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'department_id' => $validated['department_id'],
                'qualification' => $validated['qualification'],
                'specialization' => $validated['specialization'],
                'consultation_fee' => $validated['consultation_fee'],
                'status' => $validated['status'] ?? 'active',
                'image' => $imagePath,
            ]);

            return $doctor;
        });

        return response()->json([
            'status' => true,
            'message' => 'Doctor created successfully!',
            'data' => $doctor->load('user', 'department'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $doctor = Doctor::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $doctor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DoctorRequest $request, Doctor $doctor)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $doctor, $request) {

            // Step 1: User (name, email) update karo
            $doctor->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            // Step 2: Password sirf tab update ho agar diya ho
            if (! empty($validated['password'])) {
                $doctor->user->update([
                    'password' => Hash::make($validated['password']),
                ]);
            }

            // Step 3: Image update logic
            $imagePath = $doctor->image;   // Default: purani image path hi rakho

            if ($request->hasFile('image')) {
                // Purani image delete karo (agar exist karti ho)
                if ($doctor->image && Storage::disk('public')->exists($doctor->image)) {
                    Storage::disk('public')->delete($doctor->image);
                }

                // Nayi image upload karo
                $imagePath = $request->file('image')->store('doctors', 'public');
            }

            // Step 4: Doctor table update karo
            $doctor->update([
                'department_id' => $validated['department_id'],
                'qualification' => $validated['qualification'],
                'specialization' => $validated['specialization'],
                'consultation_fee' => $validated['consultation_fee'],
                'status' => $validated['status'] ?? $doctor->status,
                'image' => $imagePath,
            ]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Doctor updated successfully!',
            'data' => $doctor->fresh()->load('user', 'department'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $doctor = Doctor::findOrFail($id);

        DB::transaction(function () use ($doctor) {
            $user = $doctor->user;
            $doctor->delete();
            $user->delete();
        });

        return response()->json([
            'status' => true,
            'message' => 'doctor deleted successfully!',
        ]);
    }
}
