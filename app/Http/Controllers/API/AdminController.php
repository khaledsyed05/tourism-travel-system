<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|in:admin,travel_agent,customer',
        ]);

        $user = User::findOrFail($request->user_id);
        $role = Role::where('name', $request->role)->where('guard_name', 'api')->first();

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $user->syncRoles([$role->name]);

        return response()->json(['message' => 'Role assigned successfully']);
    }

    public function listRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }
}
