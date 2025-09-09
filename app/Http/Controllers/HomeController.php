<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientProject;
use App\Models\Invoice;
use App\Models\ProjectServiceDetail;
use Illuminate\Http\Request;
use App\Models\ProjectTask;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function adminHome()
    {   
        $totalClients = Client::count();
        $activeProjects = ClientProject::where('status', 2)->count();
        $onGoingServices = ProjectServiceDetail::where('status', 1)->count();
        $pendingInvoices = Invoice::where('status', 1)->sum('net_amount');
        $todoTasks = ProjectTask::where('status', 1)->count();
        $inProgressTasks = ProjectTask::where('status', 2)->count();
        $doneNotConfirmedTasks = ProjectTask::where('status', 3)->where('is_confirmed', 0)->count();
        $doneTasks = ProjectTask::where('status', 3)->where('is_confirmed', 1)->count();
        $now = Carbon::now()->format('Y-m-d');
        $monthlyLimit = Carbon::now()->addDays(7)->format('Y-m-d');
        $yearlyLimit = Carbon::now()->addMonths(3)->format('Y-m-d');
        $servicesExpiringSoon = ProjectServiceDetail::where('status', 1)
            ->where(function($query) use ($monthlyLimit, $yearlyLimit) {
                $query->where(function($q) use ($monthlyLimit) {
                    $q->where('cycle_type', 1)
                      ->whereRaw("STR_TO_DATE(end_date, '%Y-%m-%d') <= ?", [$monthlyLimit]);
                })
                ->orWhere(function($q) use ($yearlyLimit) {
                    $q->where('cycle_type', 2)
                      ->whereRaw("STR_TO_DATE(end_date, '%Y-%m-%d') <= ?", [$yearlyLimit]);
                });
            })
        ->count();
        $currentMonthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentMonthEnd   = Carbon::now()->endOfMonth()->format('Y-m-d');

        $currentMonthDue = ProjectServiceDetail::where('bill_paid', 0)
            ->whereRaw("STR_TO_DATE(start_date, '%Y-%m-%d') >= ?", [$currentMonthStart])
            ->whereRaw("STR_TO_DATE(start_date, '%Y-%m-%d') <= ?", [$currentMonthEnd])
            ->sum('amount');

            
        $nextMonthStart = Carbon::now()->addMonth()->startOfMonth()->format('Y-m-d');
        $nextMonthEnd   = Carbon::now()->addMonth()->endOfMonth()->format('Y-m-d');

        $nextMonthDue = ProjectServiceDetail::where('bill_paid', 0)
            ->whereRaw("STR_TO_DATE(start_date, '%Y-%m-%d') >= ?", [$nextMonthStart])
            ->whereRaw("STR_TO_DATE(start_date, '%Y-%m-%d') <= ?", [$nextMonthEnd])
            ->sum('amount');
        $today = Carbon::now()->format('Y-m-d');

        $allPreviousDue = ProjectServiceDetail::where('bill_paid', 0)
            ->whereRaw("STR_TO_DATE(start_date, '%Y-%m-%d') < ?", [$today])
            ->sum('amount');

        return view('admin.dashboard', compact('totalClients', 'activeProjects', 'onGoingServices', 'pendingInvoices', 'todoTasks', 'inProgressTasks', 'doneTasks', 'doneNotConfirmedTasks', 'servicesExpiringSoon', 'currentMonthDue', 'nextMonthDue', 'allPreviousDue'));
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
        $onGoingTasksCount = ProjectTask::where('client_id', $user->client->id)->whereIn('status', [1, 2])->where('allow_client', 1)->count();
        $notConfirmedTasksCount = ProjectTask::where('client_id', $user->client->id)->where('status', 3)->where('is_confirmed', 0)->where('allow_client', 1)->count();
        $outstandingAmount = ProjectServiceDetail::where('client_id', $user->client->id)->where('bill_paid', 0)->sum('amount');
        return view('user.dashboard', compact('ongoingprojectsCount', 'doneProjectsCount', 'plannedprojectsCount', 'onGoingTasksCount', 'notConfirmedTasksCount', 'outstandingAmount'));
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

    public function cleanDB()
    {
        $tables = [
            'activity_log',
            'client_email_logs',
            'client_projects',
            'client_types',
            'invoices',
            'invoice_details',
            'login_records',
            'project_recent_updates',
            'project_services',
            'project_service_details',
            'project_tasks',
            'task_messages',
            'task_message_views',
            'transactions',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return "Cleaned successfully.";
    }
}