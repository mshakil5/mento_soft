<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientProject;
use App\Models\Invoice;
use App\Models\ProjectServiceDetail;
use Illuminate\Http\Request;
use App\Models\ProjectTask;

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
        $todoTasks = ProjectTask::where('status', 1)->count();
        $inProgressTasks = ProjectTask::where('status', 2)->count();
        return view('admin.dashboard', compact('totalClients', 'activeProjects', 'onGoingServices', 'totalPending', 'todoTasks', 'inProgressTasks'));
    }

    public function managerHome()
    {
        return view('home');
    }

    public function userHome()
    {   
        $user = auth()->user();
        $plannedprojectsCount = ClientProject::where('client_id', $user->client->id)->where('status', 1)->count();
        $ongoingprojectsCount = ClientProject::where('client_id', $user->client->id)->where('status', 2)->count();
        $doneProjectsCount = ClientProject::where('client_id', $user->client->id)->where('status', 4)->count();
        $onGoingTasksCount = ProjectTask::where('client_id', $user->client->id)->whereIn('status', [1, 2])->count();
        $notConfirmedTasksCount = ProjectTask::where('client_id', $user->client->id)->where('status', 3)->where('is_confirmed', 0)->count();
        return view('user.dashboard', compact('ongoingprojectsCount', 'doneProjectsCount', 'plannedprojectsCount', 'onGoingTasksCount', 'notConfirmedTasksCount'));
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
            return redirect()->route('homepage');
        }
    }
}
