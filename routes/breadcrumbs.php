<?php
// routes/breadcrumbs.php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
});

// Crime
Breadcrumbs::for('crime.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Crime', route('crime.boards.index'));
});

// Criminal Cases
Breadcrumbs::for('crime.criminalCase.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.index');
    $trail->push('Criminal Cases', route('crime.criminalCase.index'));
});

Breadcrumbs::for('crime.criminalCase.show', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('crime.criminalCase.index');
    $trail->push('Case Details', route('crime.criminalCase.show', $id));
});

// Offence
Breadcrumbs::for('crime.offence.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.index');
    $trail->push('Offences', route('crime.offence.index'));
});

// Offence Category
Breadcrumbs::for('crime.category.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.index');
    $trail->push('Offence Categories', route('crime.category.index'));
});

// Accused
Breadcrumbs::for('crime.accused.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.index');
    $trail->push('Accused', route('crime.accused.index'));
});

// Island
Breadcrumbs::for('crime.island.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.index');
    $trail->push('Islands', route('crime.island.index'));
});

// Council
Breadcrumbs::for('crime.council.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.index');
    $trail->push('Councils', route('crime.council.index'));
});

// Victim
Breadcrumbs::for('crime.victim.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.index');
    $trail->push('Victims', route('crime.victim.index'));
});

// Reasons for Closure
Breadcrumbs::for('crime.reason.index', function (BreadcrumbTrail $trail) {
    $trail->parent('crime.index');
    $trail->push('Reasons for Closure', route('crime.reason.index'));
});
