<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * The Crime module's controllers (CriminalCaseController, CaseReviewController,
     * AppealDetailController, CourtOfAppealController, etc.) call ->role('cm.user')
     * and check hasRole('cm.admin') unconditionally — if these role rows don't
     * exist yet, Spatie throws RoleDoesNotExist and every one of those pages
     * 500s. UserSeeder creates these locally, but it also creates/overwrites
     * specific demo accounts with known passwords, so it's not safe to run
     * against a real database. This migration only ever inserts the three
     * role rows themselves (idempotent via firstOrCreate), so it's safe to
     * run anywhere `php artisan migrate` runs, including production.
     */
    public function up(): void
    {
        foreach (['cm.admin', 'cm.user', 'cm.registrar'] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Intentionally left as a no-op: rolling this back would delete role
        // rows that real users may already be assigned to (via
        // model_has_roles), silently breaking their access.
    }
};
