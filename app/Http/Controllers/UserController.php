<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ClientProject;
use App\Models\ProjectTask;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectServiceDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CompanyDetails;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    public function userProfile()
    {
        $user = auth()->user()->load('client');
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'business_name'   => 'required|string|max:255',
            'primary_contact' => 'required|string|max:255',
            'phone1'          => 'required|string|max:20',
            'address'         => 'nullable|string|max:500',
            'password'        => 'nullable|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        $user->name = $request->business_name;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $user->client->update([
            'business_name'   => $request->business_name,
            'primary_contact' => $request->primary_contact,
            'phone1'          => $request->phone1,
            'address'         => $request->address,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function userPassword()
    {
        return view('user.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }

    public function projects()
    {
        $user = auth()->user();

        $perPage = 10;

        $projects = ClientProject::with([
            'tasks' => fn($q) => $q->where('allow_client', 1)->latest()->with('employee'),
            'recentUpdates' => fn($q) => $q->latest(),
            'services' => fn($q) => $q->where('bill_paid', 1)->where('client_id', $user->client->id),
            'invoiceDetails.invoice' => fn($q) => $q->where('status', 2)
        ])
        ->where('client_id', $user->client->id)
        ->latest()
        ->paginate($perPage);

        $projects->getCollection()->transform(function ($project) {
            $project->totalReceived = $project->invoiceDetails
                ->filter(fn($detail) => $detail->invoice && $detail->invoice->status == 2)
                ->sum('total_inc_vat');

            return $project;
        });

        return view('user.projects', compact('projects'));
    }

    public function tasks(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;

        $projects = ClientProject::where('client_id', $user->client->id)
            ->whereHas('tasks')
            ->get();

        $tab = $request->tab ?? 'all';

        $query = ProjectTask::with(['employee', 'clientProject'])
            ->withCount(['messages as unread_messages_count' => function ($q) use ($userId) {
                $q->where('user_id', '!=', $userId)
                  ->whereDoesntHave('views', fn($q2) => $q2->where('user_id', $userId));
            }])
            ->where('client_id', $user->client->id)
            ->where('allow_client', 1)
            ->when($request->project, fn($q, $projectId) => $q->where('client_project_id', $projectId));

        switch($tab) {
            case 'tobeconfirmed':
                $query->where('status', 3)->where('is_confirmed', 0);
                break;
            case 'inprogress':
                $query->where('status', 2);
                break;
            case 'todo':
                $query->where('status', 1);
                break;
            case 'confirmed':
                $query->where('status', 3)->where('is_confirmed', 1);
                break;
            default:
                break;
        }

        $tasks = $query->latest()->paginate(10)->withQueryString();

        return view('user.tasks', compact('tasks', 'projects', 'tab'));
    }

    public function transactions(Request $request)
    {
        $clientId = auth()->user()->client->id;

        $invoiceReceivables = Transaction::with('invoice.details.clientProject', 'invoice.client')
            ->whereHas('invoice')
            ->where('transaction_type', 'Due')
            ->whereDoesntHave('invoice.transactions', function ($q) {
                $q->where('transaction_type', 'Received');
            })
            ->where('client_id', $clientId)
            ->get();

        // Invoice Received
        $invoiceReceived = Transaction::with('invoice.details.clientProject', 'invoice.client')
            ->whereHas('invoice')
            ->where('transaction_type', 'Received')
            ->where('client_id', $clientId)
            ->get();

        // Service Receivables
        $serviceReceivables = Transaction::with(['projectServiceDetail.project', 'projectServiceDetail.serviceType', 'projectServiceDetail.client'])
            ->where('transaction_type', 'Due')
            ->whereHas('projectServiceDetail')
            ->whereDoesntHave('projectServiceDetail.transactions', function ($q) {
                $q->where('transaction_type', 'Received');
            })
            ->where('client_id', $clientId)
            ->get();

        // Service Received
        $serviceReceived = Transaction::with(['projectServiceDetail.project', 'projectServiceDetail.serviceType', 'projectServiceDetail.client'])
            ->where('transaction_type', 'Received')
            ->whereHas('projectServiceDetail')
            ->where('client_id', $clientId)
            ->get();

        $serviceReceivables = $serviceReceivables->filter(function($row) {
            if (!$row->projectServiceDetail) return false;

            $start = Carbon::parse($row->projectServiceDetail->start_date ?? now());

            return match ($row->projectServiceDetail->cycle_type) {
                2 => $start < now() || now()->diffInMonths($start) <= 3,
                1 => $start < now() || now()->diffInDays($start) <= 10,
                default => false,
            };
        });

        $serviceReceived = $serviceReceived->filter(function($row) {
            if (!$row->projectServiceDetail) return false;

            $start = Carbon::parse($row->projectServiceDetail->start_date ?? now());

            return match ($row->projectServiceDetail->cycle_type) {
                2 => $start < now() || now()->diffInMonths($start) <= 3,
                1 => $start < now() || now()->diffInDays($start) <= 10,
                default => false,
            };
        });

        // Merge all
        $allTransactions = $invoiceReceivables
            ->merge($invoiceReceived)
            ->merge($serviceReceivables)
            ->merge($serviceReceived)
            ->sortByDesc('id')
            ->values();

        if ($request->status === 'Due') {
            $allTransactions = $allTransactions->filter(fn($t) => $t->transaction_type === 'Due')->values();
        }

        if ($request->type === 'Project') {
            $allTransactions = $allTransactions->filter(fn($t) => empty($t->projectServiceDetail))->values();
        }

        // Pagination manually
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $allTransactions->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginated = new LengthAwarePaginator(
            $currentItems,
            $allTransactions->count(),
            $perPage,
            $currentPage,
            ['path' => url()->current()]
        );

        $data = $paginated->getCollection()->map(function ($row, $key) {
            $isInvoice = !empty($row->invoice);
            $clientName = $isInvoice ? $row->invoice->client?->business_name ?? '-' : $row->projectServiceDetail?->client?->business_name ?? '-';
            $invoiceNo = $isInvoice ? $row->invoice->invoice_number : '-';
            $project = $isInvoice ? $row->project?->title ?? 'Custom' : $row->projectServiceDetail?->project?->title ?? '-';
            $service = $isInvoice ? '-' : $row->projectServiceDetail?->serviceType?->name ?? '-';
            $duration = $isInvoice ? '-' 
                : ($row->projectServiceDetail?->start_date && $row->projectServiceDetail?->end_date
                    ? Carbon::parse($row->projectServiceDetail->start_date)->format('d-m-Y') . ' to ' . Carbon::parse($row->projectServiceDetail->end_date)->format('d-m-Y')
                    : '-');
            $paymentDate = $row->transaction_type === 'Received' ? Carbon::parse($row->date)->format('d-m-Y') : '-';
            $method = $row->transaction_type === 'Received' ? $row->payment_type : '-';

            if ($row->transaction_type === 'Received') {
                $status = 'Received';
            } elseif ($isInvoice) {
                $status = Carbon::parse($row->date)->startOfDay() < Carbon::today() ? 'Overdue' : 'Due';
            } else {
                $serviceStart = Carbon::parse($row->projectServiceDetail->start_date);
                if ($row->projectServiceDetail->cycle_type == 2) {
                    $status = ($serviceStart < now() || now()->diffInMonths($serviceStart) <= 3) ? 'Due' : '-';
                } elseif ($row->projectServiceDetail->cycle_type == 1) {
                    $status = ($serviceStart < now() || now()->diffInDays($serviceStart) <= 10) ? 'Due' : '-';
                } else {
                    $status = '-';
                }
                if ($serviceStart < now()) {
                    $status = 'Overdue';
                }
            }

            return [
                'client_name' => $clientName,
                'invoice_no'  => $invoiceNo,
                'project'     => $project,
                'service'     => $service,
                'duration'    => $duration,
                'payment_date'=> $paymentDate,
                'amount'      => '£' . number_format($row->at_amount, 0),
                'method'      => $method,
                'status'      => $status,
                'txn'         => $row->tran_id ?? '-',
                'note'        => $row->transaction_type === 'Received' ? $row->description : '-',
            ];
        });

        return view('user.transactions', compact('data', 'paginated'));
    }

    public function storeTask(Request $request)
    {
        $request->validate([
            'project_id'  => 'required|exists:client_projects,id',
            'title'        => 'required|string',
            'task'        => 'required|string'
        ]);

        $project = ClientProject::findOrFail($request->project_id);

        ProjectTask::create([
            'client_project_id' => $project->id,
            'client_id'         => $project->client_id,
            'employee_id'         => $project->employee_id,
            'title'              => $request->title,
            'task'              => $request->task,
            'created_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Task created successfully!');
    }

    public function messages(ProjectTask $task)
    {
        $userId = auth()->id();

        $messages = $task->messages()->with('sender:id,name')->orderBy('created_at','asc')->get();

        foreach ($messages as $message) {
            if (!$message->views()->where('user_id', $userId)->exists()) {
                $message->views()->create(['user_id' => $userId]);
            }
        }

        $html = view('user.task_messages', compact('messages'))->render();

        return response()->json(['html' => $html]);
    }

    public function storeMessage(Request $request, ProjectTask $task)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = $task->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        $messages = $task->messages()->with('sender:id,name')->orderBy('created_at','asc')->get();
        $html = view('user.task_messages', compact('messages'))->render();

        return response()->json(['html' => $html]);
    }

    public function updateTask(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string',
            'title'       => 'required|string'
        ]);

        $task = ProjectTask::findOrFail($id);
        $task->title = $request->title;
        $task->task = $request->description;
        $task->save();

        return redirect()->back()->with('success', 'Task updated successfully!');
    }

    public function confirmTask(ProjectTask $task)
    {
        $task->is_confirmed = $task->is_confirmed ? 0 : 1;
        $task->save();
          
        return back()->with('success', 'Task status updated.');
    }

    public function services(Request $request)
    {
        $user = auth()->user();
        $clientId = $user->client->id;
        $today = now();

        $projects = ClientProject::where('client_id', $clientId)->get();

        $idsToDisplay = ProjectServiceDetail::where('client_id', $clientId)
            ->when($request->bill_paid !== null, fn($q) => $q->where('bill_paid', $request->bill_paid))
            ->selectRaw('MIN(id) as id')
            ->groupBy('project_service_id','client_id','client_project_id','amount','cycle_type','is_auto','bill_paid','type','is_renewed')
            ->pluck('id')
            ->toArray();

        $services = ProjectServiceDetail::with(['serviceType','project','transaction'])
            ->whereIn('id', $idsToDisplay)
            ->when($request->project, fn($q,$p) => $q->where('client_project_id', $p))
            ->get()
            ->filter(function ($row) use ($today) {
                if ($row->bill_paid == 1) {
                    return true;
                }

                $start = Carbon::parse($row->start_date ?? $today);

                return match ($row->cycle_type) {
                    1 => $start <= $today && $today->diffInDays($start) <= 10,
                    2 => $start <= $today && $today->diffInMonths($start) <= 3,
                    default => false,
                };
            });

        return view('user.services', compact('services','projects'));
    }

    public function services2(Request $request)
    {
        $user = auth()->user();
        $clientId = $user->client->id;
        $today = now();
        $projects = ClientProject::where('client_id', $clientId)->get();

        // Type 1: latest
        $latestType1Ids = ProjectServiceDetail::where('type', 1)
            ->where('client_id', $clientId)
            ->when($request->bill_paid !== null, fn($q) => $q->where('bill_paid', $request->bill_paid))
                ->where(function($q) use ($today) {
                    $q->where(function($q1) use ($today) {
                        $q1->where('cycle_type', 1)
                          ->where('end_date', '<=', $today)
                          ->where('end_date', '>=', $today->copy()->subDays(10));
                    })
                    ->orWhere(function($q2) use ($today) {
                        $q2->where('cycle_type', 2)
                          ->where('end_date', '<=', $today)
                          ->where('end_date', '>=', $today->copy()->subMonths(3));
                    });
                })
            ->selectRaw('MAX(id) as id')
            ->groupBy('project_service_id','client_id','client_project_id','amount','cycle_type','is_auto')
            ->pluck('id')->toArray();

        // Type 2: unpaid first
        $type2UnpaidIds = ProjectServiceDetail::where('type', 2)
            ->where('client_id', $clientId)
            ->when($request->bill_paid !== null, fn($q) => $q->where('bill_paid', $request->bill_paid))
            // ->where('bill_paid', 0)
                ->where(function($q) use ($today) {
                    $q->where(function($q1) use ($today) {
                        $q1->where('cycle_type', 1)
                          ->where('end_date', '<=', $today)
                          ->where('end_date', '>=', $today->copy()->subDays(10));
                    })
                    ->orWhere(function($q2) use ($today) {
                        $q2->where('cycle_type', 2)
                          ->where('end_date', '<=', $today)
                          ->where('end_date', '>=', $today->copy()->subMonths(3));
                    });
                })
            ->selectRaw('MAX(id) as id')
            ->groupBy('project_service_id','client_id','client_project_id','amount','cycle_type','is_auto')
            ->pluck('id')->toArray();

        if (!empty($type2UnpaidIds)) {
            $type2Ids = $type2UnpaidIds;
        } elseif (ProjectServiceDetail::where('type', 2)->where('client_id', $clientId)->where('is_renewed', 1)->exists()) {
            $type2Ids = ProjectServiceDetail::where('type', 2)
                ->where('client_id', $clientId)
                ->where('is_renewed', 1)
                ->selectRaw('MAX(id) as id')
                ->groupBy('project_service_id','client_id','client_project_id','amount','cycle_type','is_auto')
                ->pluck('id')->toArray();
        } else {
            $type2Ids = ProjectServiceDetail::where('type', 2)
                ->where('client_id', $clientId)
                ->where('bill_paid', 1)
                ->where('is_renewed', 0)
                ->selectRaw('MAX(id) as id')
                ->groupBy('project_service_id','client_id','client_project_id','amount','cycle_type','is_auto')
                ->pluck('id')->toArray();
        }

        $idsToDisplay = array_merge($latestType1Ids, $type2Ids);

        $services = ProjectServiceDetail::with(['serviceType','project','transaction'])
                ->where('client_id', $clientId)
                ->whereIn('id', $idsToDisplay)
                ->when($request->project, fn($q,$p) => $q->where('client_project_id', $p))
                ->get()
                ->filter(function($row) {
                    $start = Carbon::parse($row->start_date ?? now());
                    return match($row->cycle_type) {
                        1 => $start <= now() || now()->diffInDays($start) <= 10,
                        2 => $start <= now() || now()->diffInMonths($start) <= 3,
                        default => false,
                    };
                });

        return view('user.services', compact('services','projects'));
    }

    public function downloadInvoice($id)
    {
        $detail = ProjectServiceDetail::with(['serviceType', 'project', 'client'])->findOrFail($id);
        $company = CompanyDetails::first();
        $totalAmount = $detail->amount;
        $pdf = Pdf::loadView('user.invoice', compact('detail', 'company', 'totalAmount'));
        return $pdf->download("Invoice_{$detail->id}.pdf");
    }
}
