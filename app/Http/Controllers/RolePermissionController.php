<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionController extends Controller
{
    public function index()
    {
        return view('roles.index', [
            'roles' => Role::all(),
            'permissions' => Permission::all(),
            'users' => User::all(),
        ]);
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('success', 'User created');
    }

    public function storeRole(Request $request)
    {
        Role::create(['name' => $request->name]);
        return back()->with('success', 'Role created');
    }

    public function storePermission(Request $request)
    {
        Permission::create(['name' => $request->name]);
        return back()->with('success', 'Permission created');
    }

    public function assignRole(Request $request)
    {
        $user = User::find($request->user_id);
        $user->assignRole($request->role);

        return back()->with('success', 'Role assigned to user');
    }
}
