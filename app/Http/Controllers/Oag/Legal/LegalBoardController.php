<?php

namespace App\Http\Controllers\Oag\Legal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class LegalBoardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index()
    {
        
        return view('oag.legal.index');
    }
}
