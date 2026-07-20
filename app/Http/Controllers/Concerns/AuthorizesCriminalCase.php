<?php

namespace App\Http\Controllers\Concerns;

use App\Models\OAG\Crime\AgReview;
use App\Models\OAG\Crime\CriminalCase;
use App\Models\OAG\Crime\RegistryDispatch;
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

    /**
     * No case may advance to court filing without a recorded AG approval
     * followed by a Registry dispatch — this is what actually enforces that
     * rule (AgReviewController/RegistryDispatchController only gate their
     * own creation, not Court Case's).
     */
    private function assertCaseIsDispatched(CriminalCase $case): void
    {
        abort_unless(
            RegistryDispatch::where('case_id', $case->id)->exists(),
            403,
            'This case must be dispatched by the Registry (following AG approval) before it can be filed in court.'
        );
    }

    /**
     * Appeal and Court of Appeal filings are gated the same way as Court
     * Case: nothing moves forward without the AG's approval on record. The
     * Workflow dropdown only offers "Appeal"/"Court of Appeal" once
     * latest_ag_decision is 'approved' — this is the server-side half of
     * that same rule, since the UI gate alone doesn't stop a direct request.
     */
    private function assertAgHasApprovedCase(CriminalCase $case): void
    {
        $latestDecision = AgReview::where('case_id', $case->id)
            ->orderByDesc('id')
            ->value('ag_decision');

        abort_unless(
            $latestDecision === 'approved',
            403,
            'This case must be approved by the Attorney General before an appeal can be filed.'
        );
    }
}
