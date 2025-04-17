<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

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
