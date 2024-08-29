<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $role = Role::where('name', $request->role)->firstOrFail();
        $user->syncRoles($role);

        return response()->json(['message' => 'Role assigned successfully']);
    }

    public function listRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }
}
