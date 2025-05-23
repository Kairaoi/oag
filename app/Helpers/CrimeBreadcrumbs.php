<?php

/**
 * Generate breadcrumbs for Crime module routes
 * Kareke breadcrumbs ibukin te crime namespace routes
 */
function getCrimeBreadcrumbs($routeName, $parameters = [])
{
    $breadcrumbs = [];
    
    // Base breadcrumb - Home
    $breadcrumbs[] = [
        'name' => 'Home',
        'url' => route('dashboard'), // or your home route
        'active' => false
    ];
    
    // Crime Dashboard
    $breadcrumbs[] = [
        'name' => 'Crime Management',
        'url' => route('crime.boards.index'),
        'active' => false
    ];
    
    // Parse route name and build breadcrumbs
    $routeParts = explode('.', str_replace('crime.', '', $routeName));
    
    switch ($routeParts[0]) {
        case 'boards':
            $breadcrumbs[] = [
                'name' => 'Crime Boards',
                'url' => route('crime.boards.index'),
                'active' => true
            ];
            break;
            
        case 'criminalCase':
            $breadcrumbs[] = [
                'name' => 'Criminal Cases',
                'url' => route('crime.criminalCase.index'),
                'active' => false
            ];
            
            if (isset($routeParts[1])) {
                switch ($routeParts[1]) {
                    case 'create':
                        $breadcrumbs[] = [
                            'name' => 'Create New Case',
                            'url' => null,
                            'active' => true
                        ];
                        break;
                    case 'show':
                        $caseId = $parameters['id'] ?? 'Case';
                        $breadcrumbs[] = [
                            'name' => "Case #{$caseId}",
                            'url' => null,
                            'active' => true
                        ];
                        break;
                    case 'edit':
                        $caseId = $parameters['id'] ?? 'Case';
                        $breadcrumbs[] = [
                            'name' => "Edit Case #{$caseId}",
                            'url' => null,
                            'active' => true
                        ];
                        break;
                    case 'allocateForm':
                        $caseId = $parameters['id'] ?? 'Case';
                        $breadcrumbs[] = [
                            'name' => "Case #{$caseId}",
                            'url' => route('crime.criminalCase.show', $caseId),
                            'active' => false
                        ];
                        $breadcrumbs[] = [
                            'name' => 'Allocate Lawyer',
                            'url' => null,
                            'active' => true
                        ];
                        break;
                    case 'createAccused':
                        $caseId = $parameters['id'] ?? 'Case';
                        $breadcrumbs[] = [
                            'name' => "Case #{$caseId}",
                            'url' => route('crime.criminalCase.show', $caseId),
                            'active' => false
                        ];
                        $breadcrumbs[] = [
                            'name' => 'Add Accused',
                            'url' => null,
                            'active' => true
                        ];
                        break;
                    case 'createVictim':
                        $caseId = $parameters['id'] ?? 'Case';
                        $breadcrumbs[] = [
                            'name' => "Case #{$caseId}",
                            'url' => route('crime.criminalCase.show', $caseId),
                            'active' => false
                        ];
                        $breadcrumbs[] = [
                            'name' => 'Add Victim',
                            'url' => null,
                            'active' => true
                        ];
                        break;
                    case 'createIncident':
                        $caseId = $parameters['id'] ?? 'Case';
                        $breadcrumbs[] = [
                            'name' => "Case #{$caseId}",
                            'url' => route('crime.criminalCase.show', $caseId),
                            'active' => false
                        ];
                        $breadcrumbs[] = [
                            'name' => 'Add Incident',
                            'url' => null,
                            'active' => true
                        ];
                        break;
                    case 'showReallocationForm':
                        $caseId = $parameters['id'] ?? 'Case';
                        $breadcrumbs[] = [
                            'name' => "Case #{$caseId}",
                            'url' => route('crime.criminalCase.show', $caseId),
                            'active' => false
                        ];
                        $breadcrumbs[] = [
                            'name' => 'Reallocate Case',
                            'url' => null,
                            'active' => true
                        ];
                        break;
                }
            } else {
                $breadcrumbs[count($breadcrumbs) - 1]['active'] = true;
            }
            break;
            
        case 'CaseReview':
            $breadcrumbs[] = [
                'name' => 'Case Reviews',
                'url' => route('crime.CaseReview.index'),
                'active' => false
            ];
            
            if (isset($routeParts[1])) {
                switch ($routeParts[1]) {
                    case 'create':
                        $caseId = $parameters['id'] ?? '';
                        $breadcrumbs[] = [
                            'name' => "Review Case #{$caseId}",
                            'url' => null,
                            'active' => true
                        ];
                        break;
                    case 'show':
                        $reviewId = $parameters['id'] ?? 'Review';
                        $breadcrumbs[] = [
                            'name' => "Review #{$reviewId}",
                            'url' => null,
                            'active' => true
                        ];
                        break;
                }
            } else {
                $breadcrumbs[count($breadcrumbs) - 1]['active'] = true;
            }
            break;
            
        case 'offence':
            $breadcrumbs[] = [
                'name' => 'Offences',
                'url' => route('crime.offence.index'),
                'active' => isset($routeParts[1]) ? false : true
            ];
            
            if (isset($routeParts[1])) {
                switch ($routeParts[1]) {
                    case 'create':
                        $breadcrumbs[] = [
                            'name' => 'Create Offence',
                            'url' => null,
                            'active' => true
                        ];
                        break;
                    case 'edit':
                        $breadcrumbs[] = [
                            'name' => 'Edit Offence',
                            'url' => null,
                            'active' => true
                        ];
                        break;
                    case 'show':
                        $breadcrumbs[] = [
                            'name' => 'View Offence',
                            'url' => null,
                            'active' => true
                        ];
                        break;
                }
            }
            break;
            
        case 'category':
            $breadcrumbs[] = [
                'name' => 'Offence Categories',
                'url' => route('crime.category.index'),
                'active' => isset($routeParts[1]) ? false : true
            ];
            
            if (isset($routeParts[1])) {
                switch ($routeParts[1]) {
                    case 'create':
                        $breadcrumbs[] = ['name' => 'Create Category', 'url' => null, 'active' => true];
                        break;
                    case 'edit':
                        $breadcrumbs[] = ['name' => 'Edit Category', 'url' => null, 'active' => true];
                        break;
                    case 'show':
                        $breadcrumbs[] = ['name' => 'View Category', 'url' => null, 'active' => true];
                        break;
                }
            }
            break;
            
        case 'accused':
            $breadcrumbs[] = [
                'name' => 'Accused Persons',
                'url' => route('crime.accused.index'),
                'active' => isset($routeParts[1]) ? false : true
            ];
            
            if (isset($routeParts[1])) {
                switch ($routeParts[1]) {
                    case 'create':
                        $breadcrumbs[] = ['name' => 'Add Accused', 'url' => null, 'active' => true];
                        break;
                    case 'edit':
                        $breadcrumbs[] = ['name' => 'Edit Accused', 'url' => null, 'active' => true];
                        break;
                    case 'show':
                        $breadcrumbs[] = ['name' => 'Accused Details', 'url' => null, 'active' => true];
                        break;
                }
            }
            break;
            
        case 'victim':
            $breadcrumbs[] = [
                'name' => 'Victims',
                'url' => route('crime.victim.index'),
                'active' => isset($routeParts[1]) ? false : true
            ];
            
            if (isset($routeParts[1])) {
                switch ($routeParts[1]) {
                    case 'create':
                        $breadcrumbs[] = ['name' => 'Add Victim', 'url' => null, 'active' => true];
                        break;
                    case 'edit':
                        $breadcrumbs[] = ['name' => 'Edit Victim', 'url' => null, 'active' => true];
                        break;
                    case 'show':
                        $breadcrumbs[] = ['name' => 'Victim Details', 'url' => null, 'active' => true];
                        break;
                }
            }
            break;
            
        case 'island':
            $breadcrumbs[] = [
                'name' => 'Islands',
                'url' => route('crime.island.index'),
                'active' => isset($routeParts[1]) ? false : true
            ];
            break;
            
        case 'appeal':
            $breadcrumbs[] = [
                'name' => 'Appeals',
                'url' => route('crime.appeal.index'),
                'active' => isset($routeParts[1]) ? false : true
            ];
            
            if (isset($routeParts[1])) {
                switch ($routeParts[1]) {
                    case 'create':
                        $breadcrumbs[] = ['name' => 'Create Appeal', 'url' => null, 'active' => true];
                        break;
                    case 'show':
                        $breadcrumbs[] = ['name' => 'Appeal Details', 'url' => null, 'active' => true];
                        break;
                    case 'edit':
                        $breadcrumbs[] = ['name' => 'Edit Appeal', 'url' => null, 'active' => true];
                        break;
                }
            }
            break;
            
        case 'incident':
            $breadcrumbs[] = [
                'name' => 'Incidents',
                'url' => route('crime.incident.index'),
                'active' => isset($routeParts[1]) ? false : true
            ];
            break;
            
        case 'reason':
            $breadcrumbs[] = [
                'name' => 'Closure Reasons',
                'url' => route('crime.reason.index'),
                'active' => isset($routeParts[1]) ? false : true
            ];
            break;
            
        case 'court-hearings':
            $breadcrumbs[] = [
                'name' => 'Court Hearings',
                'url' => route('crime.court-hearings.index'),
                'active' => isset($routeParts[1]) ? false : true
            ];
            
            if (isset($routeParts[1])) {
                switch ($routeParts[1]) {
                    case 'create':
                        $breadcrumbs[] = ['name' => 'Schedule Hearing', 'url' => null, 'active' => true];
                        break;
                    case 'show':
                        $breadcrumbs[] = ['name' => 'Hearing Details', 'url' => null, 'active' => true];
                        break;
                    case 'edit':
                        $breadcrumbs[] = ['name' => 'Edit Hearing', 'url' => null, 'active' => true];
                        break;
                }
            }
            break;
            
        case 'court-cases':
        case 'CourtCase':
            $breadcrumbs[] = [
                'name' => 'Court Cases',
                'url' => route('crime.court-cases.index'),
                'active' => isset($routeParts[1]) ? false : true
            ];
            
            if (isset($routeParts[1])) {
                switch ($routeParts[1]) {
                    case 'create':
                        $breadcrumbs[] = ['name' => 'Create Court Case', 'url' => null, 'active' => true];
                        break;
                    case 'show':
                        $breadcrumbs[] = ['name' => 'Court Case Details', 'url' => null, 'active' => true];
                        break;
                    case 'edit':
                        $breadcrumbs[] = ['name' => 'Edit Court Case', 'url' => null, 'active' => true];
                        break;
                }
            }
            break;
            
        case 'reports':
            $breadcrumbs[] = [
                'name' => 'Reports',
                'url' => route('crime.reports.index'),
                'active' => isset($routeParts[1]) ? false : true
            ];
            
            if (isset($routeParts[1]) && $routeParts[1] == 'show') {
                $breadcrumbs[] = [
                    'name' => 'Report Details',
                    'url' => null,
                    'active' => true
                ];
            }
            break;
            
        // Special routes
        case 'appealcase':
            $breadcrumbs[] = [
                'name' => 'Criminal Cases',
                'url' => route('crime.criminalCase.index'),
                'active' => false
            ];
            $breadcrumbs[] = [
                'name' => 'Appeal Cases',
                'url' => null,
                'active' => true
            ];
            break;
            
        case 'courtcase':
            $breadcrumbs[] = [
                'name' => 'Criminal Cases',
                'url' => route('crime.criminalCase.index'),
                'active' => false
            ];
            $breadcrumbs[] = [
                'name' => 'Court Cases',
                'url' => null,
                'active' => true
            ];
            break;
            
        case 'casereview':
            if (isset($routeParts[1]) && $routeParts[1] == 'reviewed') {
                $breadcrumbs[] = [
                    'name' => 'Criminal Cases',
                    'url' => route('crime.criminalCase.index'),
                    'active' => false
                ];
                $breadcrumbs[] = [
                    'name' => 'Reviewed Cases',
                    'url' => null,
                    'active' => true
                ];
            }
            break;
            
        default:
            $breadcrumbs[] = [
                'name' => ucfirst($routeParts[0]),
                'url' => null,
                'active' => true
            ];
            break;
    }
    
    return $breadcrumbs;
}

/**
 * Helper function to use in Blade templates
 * Usage: {!! renderCrimeBreadcrumbs() !!}
 */
function renderCrimeBreadcrumbs($class = 'breadcrumb')
{
    $routeName = request()->route()->getName();
    $parameters = request()->route()->parameters();
    
    if (!str_starts_with($routeName, 'crime.')) {
        return '';
    }
    
    $breadcrumbs = getCrimeBreadcrumbs($routeName, $parameters);
    
    $html = '<nav aria-label="breadcrumb">';
    $html .= '<ol class="' . $class . '">';
    
    foreach ($breadcrumbs as $breadcrumb) {
        if ($breadcrumb['active']) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">';
            $html .= htmlspecialchars($breadcrumb['name']);
            $html .= '</li>';
        } else {
            $html .= '<li class="breadcrumb-item">';
            if ($breadcrumb['url']) {
                $html .= '<a href="' . $breadcrumb['url'] . '">';
                $html .= htmlspecialchars($breadcrumb['name']);
                $html .= '</a>';
            } else {
                $html .= htmlspecialchars($breadcrumb['name']);
            }
            $html .= '</li>';
        }
    }
    
    $html .= '</ol>';
    $html .= '</nav>';
    
    return $html;
}