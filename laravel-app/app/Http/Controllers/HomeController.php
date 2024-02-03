<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
// use App\Http\Model\User;




class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::id()) {
            $usertype = Auth::user()->usertype;

            if ($usertype == 'user') {
                return view('home');
            } else if ($usertype == 'admin') {
                return view('admin.adminhome');
            }
        } else {
            return redirect()->back();
        }


    }

    public function post()
    {
        echo 'Admin post';
    }
}