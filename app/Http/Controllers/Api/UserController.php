<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private const STAFF_ROLES = ['doctor', 'receptionist'];

    public function index()
    {
        $users = User::with('roles:id,name')
            ->whereHas('roles', fn ($q) => $q->whereIn('name', self::STAFF_ROLES))
            ->latest()
            ->get();

        return response()->json(['status' => true, 'data' => $users]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(self::STAFF_ROLES)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => $user->load('roles:id,name'),
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in(self::STAFF_ROLES)],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'data' => $user->load('roles:id,name'),
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        if ($request->user()->id == $id) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        User::findOrFail($id)->delete();

        return response()->json(['status' => true, 'message' => 'User deleted successfully']);
    }
}