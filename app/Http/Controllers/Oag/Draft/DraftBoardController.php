<?php

namespace App\Http\Controllers\Oag\Draft;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class DraftBoardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index()
    {
        
        return view('oag.draft.index');
    }
}
