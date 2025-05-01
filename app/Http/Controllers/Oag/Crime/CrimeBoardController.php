<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Oag\Crime\CriminalCase;
use App\Models\User;

class CrimeBoardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index()
    {
        $cases = CriminalCase::with('lawyer')->get();

        $pendingCount = $cases->where('status', 'pending')->count();
        $allocatedCount = $cases->where('status', 'allocated')->count();
        $rejectedCount = $cases->where('status', 'rejected')->count();
        $acceptedCount = $cases->where('status', 'accepted')->count();
        $lawyerCount = User::count(); 

        return view('oag.index', compact(
        'cases',
        'pendingCount',
        'allocatedCount',
        'rejectedCount',
        'lawyerCount',
        'acceptedCount'
    ));

    }

}
