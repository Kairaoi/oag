<?php

namespace App\Http\Controllers\Oag\Civil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class CivilBoardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index()
    {
        
        return view('oag.civil.index');
    }
}
