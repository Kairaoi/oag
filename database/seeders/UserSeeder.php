<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Guarantees known-password logins with the crime module's cm.admin/cm.user
     * roles for the accounts CriminalJusticeSystemSeeder creates (which inserts
     * raw rows via DB::table() and never assigns any role), so login + role
     * access survive migrate:fresh --seed instead of being patched by hand.
     */
    public function run()
    {
        Role::firstOrCreate(['name' => 'cm.admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cm.user', 'guard_name' => 'web']);
        // Registration-only role: can create/register a case (and add accused/
        // victim/incident, which carry no role restriction of their own) but
        // holds none of cm.user's powers (accept/reject, case review, court
        // case, appeal, court of appeal) or cm.admin's (allocate/reallocate).
        Role::firstOrCreate(['name' => 'cm.registrar', 'guard_name' => 'web']);
        // AG Review gate: approves/rejects a case before it can be dispatched
        // to court — distinct from cm.admin (allocation) and cm.user (review).
        Role::firstOrCreate(['name' => 'cm.ag', 'guard_name' => 'web']);

        $admin = User::updateOrCreate(
            ['email' => 'adminuser@example.com'],
            ['name' => 'Admin User', 'password' => Hash::make('Password123!')]
        );
        $admin->syncRoles(['cm.admin', 'cm.user']);

        // Named "Registrar", not "Lawyer 1" — this account only ever holds
        // cm.registrar (case intake + Registry dispatch), never cm.user, so a
        // lawyer-style name was misleading about what it can actually do.
        $registrar = User::updateOrCreate(
            ['email' => 'lawyer1@example.com'],
            ['name' => 'Registrar', 'password' => Hash::make('Password123!')]
        );
        $registrar->syncRoles(['cm.registrar']);

        foreach (['Lawyer 2', 'Lawyer 3'] as $name) {
            $lawyer = User::updateOrCreate(
                ['email' => strtolower(str_replace(' ', '', $name)) . '@example.com'],
                ['name' => $name, 'password' => Hash::make('Password123!')]
            );
            $lawyer->syncRoles(['cm.user']);
        }

        $assistant = User::updateOrCreate(
            ['email' => 'adminassistant@example.com'],
            ['name' => 'Admin Assistant', 'password' => Hash::make('Password123!')]
        );
        $assistant->syncRoles(['cm.admin']);

        $agReviewer = User::updateOrCreate(
            ['email' => 'agreviewer@example.com'],
            ['name' => 'AG Reviewer', 'password' => Hash::make('Password123!')]
        );
        $agReviewer->syncRoles(['cm.ag']);
    }
}
