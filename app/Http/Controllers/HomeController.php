<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function adminHome()
    {
        return view('admin.dashboard');
    }

    public function managerHome()
    {
        return view('home');
    }

    public function userHome()
    {
        return view('home');
    }

    public function toggleSidebar(Request $request)
    {
        $user = auth()->user();
        $user->sidebar = !$user->sidebar;
        $user->save();
        
        return redirect()->route('admin.dashboard');
    }

    public function dashboard()
    { 
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->is_type == '1') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->is_type == '2') {
                return redirect()->route('manager.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        } else {
            return redirect()->route('login');
        }
    }
}
