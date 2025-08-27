<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientProject;
use App\Models\Invoice;
use App\Models\ProjectServiceDetail;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function adminHome()
    {   
        $totalClients = Client::count();
        $activeProjects = ClientProject::where('status', 2)->count();
        $onGoingServices = ProjectServiceDetail::where('status', 1)->count();
        $totalPending = Invoice::where('status', 2)->sum('net_amount');
        return view('admin.dashboard', compact('totalClients', 'activeProjects', 'onGoingServices', 'totalPending'));
    }

    public function managerHome()
    {
        return view('home');
    }

    public function userHome()
    {   
        $user = auth()->user();
        $projectsCount = ClientProject::where('client_id', $user->client->id)->count();
        
        return view('user.dashboard', compact('projectsCount'));
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

            if ($user->user_type == '1') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->user_type == '2') {
                return redirect()->route('manager.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        } else {
            return redirect()->route('login');
        }
    }
}
