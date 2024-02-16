<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Show the application view based on user type. **/
    public function index()
    {
        if (Auth::check()) {
            $usertype = Auth::user()->usertype;

            if ($usertype == 'user') {
                return view('user.home');
            } elseif ($usertype == 'admin') {
                return view('admin.adminhome');
            }
        }

        // If user is not authenticated or usertype is not set, redirect to login
        return redirect()->route('login');
    }

    public function post()
    {
        echo 'Admin post';
    }
}