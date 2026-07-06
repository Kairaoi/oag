<?php

namespace App\Http\Controllers\Concerns;

use App\Models\OAG\Crime\CriminalCase;
use App\Models\User;

trait AuthorizesCriminalCase
{
    /**
     * A plain cm.user (no cm.admin) may only act on cases that are unassigned
     * or already assigned to them, mirroring the datatable visibility filter
     * in CriminalCaseRepository::getForDataTable(). cm.admin bypasses this.
     */
    private function assertCanActOnCase(CriminalCase $case, User $user): void
    {
        if ($user->hasRole('cm.admin')) {
            return;
        }

        if ($case->lawyer_id !== null && (int) $case->lawyer_id !== (int) $user->id) {
            abort(403, 'You are not authorized to act on this case.');
        }
    }

    /**
     * A rejected case is a dead end for every forward-moving action (allocate,
     * case review, court case, appeal, court of appeal) — the only way out of
     * "rejected" is CriminalCaseController::reallocateCase(), which does NOT
     * call this check. Every other status (including "accepted") is allowed.
     */
    private function assertCaseIsActionable(CriminalCase $case): void
    {
        if ($case->status === 'rejected') {
            abort(403, 'This case has been rejected and cannot proceed until it is reallocated.');
        }
    }

    /**
     * Related Records (Reviewed Cases / Court Cases / Appeal Cases / Court of
     * Appeal Cases) is view-only case history, gated to cm.user/cm.admin —
     * a cm.registrar (registration-only) account has neither.
     */
    private function assertCanViewRelatedRecords(): void
    {
        abort_unless(auth()->user()->hasRole('cm.user') || auth()->user()->hasRole('cm.admin'), 403);
    }
}
