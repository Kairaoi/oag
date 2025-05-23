<?php

// File: routes/breadcrumbs.php (create this file if it doesn't exist)

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
});

// Crime Management
Breadcrumbs::for('crime.boards.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Crime Management', route('crime.boards.index'));
});

// Crime Boards
Breadcrumbs::for('crime.boards.*', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.boards.index');
    $trail->push('Crime Boards');
});

// Criminal Cases
Breadcrumbs::for('crime.criminalCase.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.boards.index');
    $trail->push('Criminal Cases', route('crime.criminalCase.index'));
});

Breadcrumbs::for('crime.criminalCase.create', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.criminalCase.index');
    $trail->push('Create New Case');
});

Breadcrumbs::for('crime.criminalCase.show', function (BreadcrumbTrail $trail, $case) {
    $trail->parent('crime.criminalCase.index');
    $caseId = is_object($case) ? ($case->id ?? $case) : $case;
    $trail->push("Case #{$caseId}");
});

Breadcrumbs::for('crime.criminalCase.edit', function (BreadcrumbTrail $trail, $case) {
    $trail->parent('crime.criminalCase.index');
    $caseId = is_object($case) ? ($case->id ?? $case) : $case;
    $trail->push("Edit Case #{$caseId}");
});

Breadcrumbs::for('crime.criminalCase.allocateForm', function (BreadcrumbTrail $trail, $case) {
    $trail->parent('crime.criminalCase.show', $case);
    $trail->push('Allocate Lawyer');
});

Breadcrumbs::for('crime.criminalCase.createAccused', function (BreadcrumbTrail $trail, $case) {
    $trail->parent('crime.criminalCase.show', $case);
    $trail->push('Add Accused');
});

Breadcrumbs::for('crime.criminalCase.createVictim', function (BreadcrumbTrail $trail, $case) {
    $trail->parent('crime.criminalCase.show', $case);
    $trail->push('Add Victim');
});

Breadcrumbs::for('crime.criminalCase.createIncident', function (BreadcrumbTrail $trail, $case) {
    $trail->parent('crime.criminalCase.show', $case);
    $trail->push('Add Incident');
});

Breadcrumbs::for('crime.criminalCase.showReallocationForm', function (BreadcrumbTrail $trail, $case) {
    $trail->parent('crime.criminalCase.show', $case);
    $trail->push('Reallocate Case');
});

// Case Reviews
Breadcrumbs::for('crime.CaseReview.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.boards.index');
    $trail->push('Case Reviews', route('crime.CaseReview.index'));
});

Breadcrumbs::for('crime.CaseReview.create', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.CaseReview.index');
    $trail->push('Create Review');
});

Breadcrumbs::for('crime.CaseReview.show', function (BreadcrumbTrail $trail, $review) {
    $trail->parent('crime.CaseReview.index');
    $reviewId = is_object($review) ? ($review->id ?? $review) : $review;
    $trail->push("Review #{$reviewId}");
});

// Offences
Breadcrumbs::for('crime.offence.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.boards.index');
    $trail->push('Offences', route('crime.offence.index'));
});

Breadcrumbs::for('crime.offence.create', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.offence.index');
    $trail->push('Create Offence');
});

Breadcrumbs::for('crime.offence.show', function (BreadcrumbTrail $trail, $offence) {
    $trail->parent('crime.offence.index');
    $trail->push('View Offence');
});

Breadcrumbs::for('crime.offence.edit', function (BreadcrumbTrail $trail, $offence) {
    $trail->parent('crime.offence.index');
    $trail->push('Edit Offence');
});

// Offence Categories
Breadcrumbs::for('crime.category.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.boards.index');
    $trail->push('Offence Categories', route('crime.category.index'));
});

Breadcrumbs::for('crime.category.create', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.category.index');
    $trail->push('Create Category');
});

Breadcrumbs::for('crime.category.show', function (BreadcrumbTrail $trail, $category) {
    $trail->parent('crime.category.index');
    $trail->push('View Category');
});

Breadcrumbs::for('crime.category.edit', function (BreadcrumbTrail $trail, $category) {
    $trail->parent('crime.category.index');
    $trail->push('Edit Category');
});

// Accused Persons
Breadcrumbs::for('crime.accused.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.boards.index');
    $trail->push('Accused Persons', route('crime.accused.index'));
});

Breadcrumbs::for('crime.accused.create', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.accused.index');
    $trail->push('Add Accused');
});

Breadcrumbs::for('crime.accused.show', function (BreadcrumbTrail $trail, $accused) {
    $trail->parent('crime.accused.index');
    if (is_object($accused) && isset($accused->name)) {
        $trail->push($accused->name);
    } else {
        $accusedId = is_object($accused) ? ($accused->id ?? $accused) : $accused;
        $trail->push("Accused #{$accusedId}");
    }
});

Breadcrumbs::for('crime.accused.edit', function (BreadcrumbTrail $trail, $accused) {
    $trail->parent('crime.accused.show', $accused);
    $trail->push('Edit');
});

// Victims
Breadcrumbs::for('crime.victim.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.boards.index');
    $trail->push('Victims', route('crime.victim.index'));
});

Breadcrumbs::for('crime.victim.create', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.victim.index');
    $trail->push('Add Victim');
});

Breadcrumbs::for('crime.victim.show', function (BreadcrumbTrail $trail, $victim) {
    $trail->parent('crime.victim.index');
    if (is_object($victim) && isset($victim->name)) {
        $trail->push($victim->name);
    } elseif (is_object($victim) && isset($victim->id)) {
        $trail->push("Victim #{$victim->id}");
    } else {
        $trail->push("Victim #{$victim}");
    }
});

Breadcrumbs::for('crime.victim.edit', function (BreadcrumbTrail $trail, $victim) {
    $trail->parent('crime.victim.show', $victim);
    $trail->push('Edit');
});

// Islands
Breadcrumbs::for('crime.island.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.boards.index');
    $trail->push('Islands', route('crime.island.index'));
});

// Appeals
Breadcrumbs::for('crime.appeal.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.boards.index');
    $trail->push('Appeals', route('crime.appeal.index'));
});

Breadcrumbs::for('crime.appeal.create', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.appeal.index');
    $trail->push('Create Appeal');
});

Breadcrumbs::for('crime.appeal.show', function (BreadcrumbTrail $trail, $appeal) {
    $trail->parent('crime.appeal.index');
    $trail->push('Appeal Details');
});

Breadcrumbs::for('crime.appeal.edit', function (BreadcrumbTrail $trail, $appeal) {
    $trail->parent('crime.appeal.index');
    $trail->push('Edit Appeal');
});

// Incidents
Breadcrumbs::for('crime.incident.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.boards.index');
    $trail->push('Incidents', route('crime.incident.index'));
});

// Closure Reasons
Breadcrumbs::for('crime.reason.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.boards.index');
    $trail->push('Closure Reasons', route('crime.reason.index'));
});

// Court Hearings
Breadcrumbs::for('crime.court-hearings.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.boards.index');
    $trail->push('Court Hearings', route('crime.court-hearings.index'));
});

Breadcrumbs::for('crime.court-hearings.create', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.court-hearings.index');
    $trail->push('Schedule Hearing');
});

Breadcrumbs::for('crime.court-hearings.show', function (BreadcrumbTrail $trail, $hearing) {
    $trail->parent('crime.court-hearings.index');
    $trail->push('Hearing Details');
});

Breadcrumbs::for('crime.court-hearings.edit', function (BreadcrumbTrail $trail, $hearing) {
    $trail->parent('crime.court-hearings.index');
    $trail->push('Edit Hearing');
});

// Court Cases
Breadcrumbs::for('crime.court-cases.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.boards.index');
    $trail->push('Court Cases', route('crime.court-cases.index'));
});

Breadcrumbs::for('crime.court-cases.create', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.court-cases.index');
    $trail->push('Create Court Case');
});

Breadcrumbs::for('crime.court-cases.show', function (BreadcrumbTrail $trail, $courtCase) {
    $trail->parent('crime.court-cases.index');
    $trail->push('Court Case Details');
});

Breadcrumbs::for('crime.court-cases.edit', function (BreadcrumbTrail $trail, $courtCase) {
    $trail->parent('crime.court-cases.index');
    $trail->push('Edit Court Case');
});

// Reports
Breadcrumbs::for('crime.reports.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.boards.index');
    $trail->push('Reports', route('crime.reports.index'));
});

Breadcrumbs::for('crime.reports.show', function (BreadcrumbTrail $trail, $report) {
    $trail->parent('crime.reports.index');
    $trail->push('Report Details');
});

// Special routes
Breadcrumbs::for('crime.appealcase', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.criminalCase.index');
    $trail->push('Appeal Cases');
});

Breadcrumbs::for('crime.courtcase', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.criminalCase.index');
    $trail->push('Court Cases');
});

Breadcrumbs::for('crime.casereview.reviewed', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.criminalCase.index');
    $trail->push('Reviewed Cases');
});

// Closure Reasons - Create
Breadcrumbs::for('crime.reason.create', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.reason.index');
    $trail->push('Add Closure Reason');
});

// Closure Reasons - Edit
Breadcrumbs::for('crime.reason.edit', function (BreadcrumbTrail $trail, $reason) {
    $trail->parent('crime.reason.index');
    $trail->push('Edit Closure Reason');
});

// Closure Reasons - Show (optional, if you have a view page)
Breadcrumbs::for('crime.reason.show', function (BreadcrumbTrail $trail, $reason) {
    $trail->parent('crime.reason.index');
    $trail->push('Closure Reason Details');
});
