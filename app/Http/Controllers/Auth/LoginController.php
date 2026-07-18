<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Overrides the static $redirectTo property (AuthenticatesUsers'
     * RedirectsUsers::redirectPath() prefers this method when it exists) so
     * an AG reviewer lands on their pending review queue instead of the
     * generic case list — that's the only thing a cm.ag account needs to see
     * after logging in.
     */
    public function redirectTo(): string
    {
        if (auth()->check() && auth()->user()->hasRole('cm.ag')) {
            return route('crime.ag-reviews.index');
        }

        return $this->redirectTo;
    }
}
