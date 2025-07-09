<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }
        $users = $query->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|in:nasabah',
        ]);
        $user = User::create(
            array_merge($validated, [
                'deposit_balance' => 0.0,
                'password' => bcrypt('default_password'),
            ]),
        );
        return response()->json($user, 201);
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            Log::info("User fetched: id={$id}", ['user' => $user]);
            return response()->json($user, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching user: id={$id}, error={$e->getMessage()}");
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role' => 'sometimes|string|in:nasabah,operator',
        ]);
        $user->update($validated);
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Nasabah deleted']);
    }

    public function getTotalSetoran($id)
    {
        $user = User::findOrFail($id);
        $totalSetoran = $user->setoranSampah()->count();
        return response()->json(['total_setoran' => $totalSetoran]);
    }
}
