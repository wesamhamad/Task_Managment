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
        if (Auth::id()) {
            $usertype = Auth::user()->usertype;

            if ($usertype == 'user') {
                return view('user.home');
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