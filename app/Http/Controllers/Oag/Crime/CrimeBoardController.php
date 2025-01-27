<?php

namespace App\Http\Controllers\Oag\Crime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class CrimeBoardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index()
    {
        
        return view('oag.index');
    }
}
