@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Role & Permission Management</h2>

    <ul class="nav nav-tabs" id="rolePermissionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab">Roles</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button" role="tab">Permissions</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="assign-tab" data-bs-toggle="tab" data-bs-target="#assign" type="button" role="tab">Assign Role</button>
        </li>
    </ul>

    <div class="tab-content pt-3" id="rolePermissionTabsContent">
        {{-- Roles Tab --}}
        <div class="tab-pane fade show active" id="roles" role="tabpanel">
            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf
                <div class="mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Role name">
                </div>
                <button type="submit" class="btn btn-primary">Add Role</button>
            </form>

            <ul class="mt-3 list-group">
                @foreach($roles as $role)
                    <li class="list-group-item">{{ $role->name }}</li>
                @endforeach
            </ul>
        </div>

        {{-- Permissions Tab --}}
        <div class="tab-pane fade" id="permissions" role="tabpanel">
            <form method="POST" action="{{ route('admin.permissions.store') }}">
                @csrf
                <div class="mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Permission name">
                </div>
                <button type="submit" class="btn btn-primary">Add Permission</button>
            </form>

            <ul class="mt-3 list-group">
                @foreach($permissions as $permission)
                    <li class="list-group-item">{{ $permission->name }}</li>
                @endforeach
            </ul>
        </div>

        {{-- Assign Role Tab --}}
        <div class="tab-pane fade" id="assign" role="tabpanel">
            <h4>Assign Role to User</h4>
            <form method="POST" action="{{ route('admin.roles.assign') }}">
                @csrf
                <div class="mb-3">
                    <label>User:</label>
                    <select name="user_id" class="form-select">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Role:</label>
                    <select name="role" class="form-select">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Assign Role</button>
            </form>

            <hr>
            <h5 class="mt-4">Current User Roles</h5>
            <ul class="list-group mt-2">
                @foreach($users as $user)
                    <li class="list-group-item">
                        <strong>{{ $user->name }}</strong>
                        <br>
                        @php
                            $userRoles = $user->roles->pluck('name')->toArray();
                        @endphp
                        Role: {{ count($userRoles) ? implode(', ', $userRoles) : 'No roles assigned' }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
