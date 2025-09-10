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

        $activeProjects = ClientProject::count();

        $latestType1Ids = ProjectServiceDetail::where('type', 1)
          ->where('status', 1)
          ->selectRaw('MAX(id) as id')
          ->groupBy('project_service_id', 'client_id', 'client_project_id', 'amount', 'cycle_type', 'is_auto')
          ->pluck('id')
          ->toArray();

        $latestType2Ids = ProjectServiceDetail::where('type', 2)
          ->where('status', 1)
          ->selectRaw('MAX(id) as id')
          ->groupBy('project_service_id', 'client_id', 'client_project_id', 'amount', 'cycle_type', 'is_auto')
          ->pluck('id')
          ->toArray();

        $onGoingServices = count($latestType1Ids) + count($latestType2Ids);

        $pendingInvoices = Invoice::where('status', 1)->sum('net_amount');
        $todoTasks = ProjectTask::where('status', 1)->count();
        $inProgressTasks = ProjectTask::where('status', 2)->count();
        $doneNotConfirmedTasks = ProjectTask::where('status', 3)->where('is_confirmed', 0)->count();
        $doneTasks = ProjectTask::where('status', 3)->where('is_confirmed', 1)->count();
        $now = Carbon::now()->format('Y-m-d');
        $monthlyLimit = Carbon::now()->addDays(7)->format('Y-m-d');
        $yearlyLimit = Carbon::now()->addMonths(3)->format('Y-m-d');
        $servicesNeedToBeRenewed = ProjectServiceDetail::where('type', 2)->where('is_renewed', 0)->where('status', 1)->where('next_created', 0)->count();

        //Current Month
        $today = Carbon::now()->format('Y-m-d');
        $currentMonthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentMonthEnd   = Carbon::now()->endOfMonth()->format('Y-m-d');

        $currentMonthCount = ProjectServiceDetail::where('status', 1)
            ->where('bill_paid', 0)
            ->where(function($q) use ($currentMonthStart, $currentMonthEnd) {
                $q->whereBetween('start_date', [$currentMonthStart, $currentMonthEnd]) // current month unpaid
                  ->orWhere('start_date', '<', $currentMonthStart); // previous unpaid
            })
            ->count();

        // Next month
        $nextMonthStart = Carbon::now()->addMonth()->startOfMonth()->format('Y-m-d');
        $nextMonthEnd   = Carbon::now()->addMonth()->endOfMonth()->format('Y-m-d');

        $nextMonthCount = ProjectServiceDetail::where('status', 1)
            ->where('type', 2) // only third party
            ->where('bill_paid', 0)
            ->whereBetween('start_date', [$nextMonthStart, $nextMonthEnd])
            ->count();

        // Next 2 month
        $next2MonthStart = Carbon::now()->addMonths(2)->startOfMonth()->format('Y-m-d');
        $next2MonthEnd   = Carbon::now()->addMonths(2)->endOfMonth()->format('Y-m-d');

        $next2MonthCount = ProjectServiceDetail::where('status', 1)
            ->where('type', 2) // only third party
            ->where('bill_paid', 0)
            ->whereBetween('start_date', [$next2MonthStart, $next2MonthEnd])
            ->count();

        // Next 3 month
        $next3MonthStart = Carbon::now()->addMonths(3)->startOfMonth()->format('Y-m-d');
        $next3MonthEnd   = Carbon::now()->addMonths(3)->endOfMonth()->format('Y-m-d');

        $next3MonthCount = ProjectServiceDetail::where('status', 1)
            ->where('type', 2) // only third party
            ->where('bill_paid', 0)
            ->whereBetween('start_date', [$next3MonthStart, $next3MonthEnd])
            ->count();

        return view('admin.dashboard', compact('totalClients', 'activeProjects', 'onGoingServices', 'pendingInvoices', 'todoTasks', 'inProgressTasks', 'doneTasks', 'doneNotConfirmedTasks', 'servicesNeedToBeRenewed', 'currentMonthCount', 'nextMonthCount', 'next2MonthCount', 'next3MonthCount'));
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
            // 'client_projects',
            // 'client_types',
            'invoices',
            'invoice_details',
            'login_records',
            // 'project_recent_updates',
            // 'project_services',
            'project_service_details',
            // 'project_tasks',
            // 'task_messages',
            // 'task_message_views',
            'transactions',
            'service_renewals',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return "Cleaned successfully.";
    }
}