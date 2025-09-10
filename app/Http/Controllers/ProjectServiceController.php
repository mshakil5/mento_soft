<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectService;
use Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ProjectServiceDetail;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\ClientProject;
use Mail;
use Illuminate\Support\Facades\DB;
use App\Models\CompanyDetails;
use Illuminate\Support\Facades\Log;
use App\Models\ServiceRenewal;

class ProjectServiceController extends Controller
{
    public function index(Request $request)
    {
        $this->checkAndGenerateServices();
        if ($request->ajax()) {
            $data = ProjectServiceDetail::with(['serviceType', 'client', 'project'])->latest();

            $latestType1Ids = ProjectServiceDetail::where('type', 1)
              ->selectRaw('MAX(id) as id')
              ->groupBy('project_service_id', 'client_id', 'client_project_id', 'amount', 'cycle_type', 'is_auto')
              ->pluck('id')
              ->toArray();

            $type2UnpaidIds = ProjectServiceDetail::where('type', 2)
              // ->where('bill_paid', 0)
              ->selectRaw('MAX(id) as id')
              ->groupBy('project_service_id', 'client_id', 'client_project_id', 'amount', 'cycle_type', 'is_auto')
              ->pluck('id')
              ->toArray();

            if (!empty($type2UnpaidIds)) {
                // Case 1: Unpaid exists → show latest unpaid
                $type2Ids = $type2UnpaidIds;
            } elseif (ProjectServiceDetail::where('type', 2)->where('is_renewed', 1)->exists()) {
                // Case 2: show renewed
                $type2Ids = ProjectServiceDetail::where('type', 2)
                    ->where('is_renewed', 1)
                    ->selectRaw('MAX(id) as id')
                    ->groupBy('project_service_id', 'client_id', 'client_project_id', 'amount', 'cycle_type', 'is_auto')
                    ->pluck('id')
                    ->toArray();
            } else {
                // Case 3: All paid → show latest NOT renewed
                $type2Ids = ProjectServiceDetail::where('type', 2)
                    ->where('bill_paid', 1)
                    ->where('is_renewed', 0)
                    ->selectRaw('MAX(id) as id')
                    ->groupBy('project_service_id', 'client_id', 'client_project_id', 'amount', 'cycle_type', 'is_auto')
                    ->pluck('id')
                    ->toArray();
            }

            $idsToDisplay = array_merge($latestType1Ids, $type2Ids);

            $data = $data->whereIn('id', $idsToDisplay);

            if ($request->client_filter_id) {
                $data = $data->where('client_id', $request->client_filter_id);
            }

            if ($request->client_id) {
                $data = $data->where('client_id', $request->client_id);
            }

            if ($request->project_filter_id) {
                $data = $data->where('client_project_id', $request->project_filter_id);
            }

            if ($request->service_filter_type_id) {
                $data = $data->where('project_service_id', $request->service_filter_type_id);
            }

            if ($request->project_service_id) {
                $data = $data->where('project_service_id', $request->project_service_id);
            }

            if ($request->has('bill_paid')) {
                $data = $data->where('bill_paid', $request->bill_paid);
            }

            if ($request->has('status')) {
                $data = $data->where('status', $request->status);
            }

            if ($request->renew == 1) {
                $data->where('type', 2)
                    ->where('is_renewed', 0)
                    ->where('status', 1)
                    ->where('next_created', 0);
            }

            if ($request->has('due')) {
                $due = $request->due;

                if ($due == 'current') {
                    $currentMonthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
                    $currentMonthEnd   = Carbon::now()->endOfMonth()->format('Y-m-d');

                    $data = $data->where('bill_paid', 0)
                        ->where(function($q) use ($currentMonthStart, $currentMonthEnd) {
                            $q->whereBetween('start_date', [$currentMonthStart, $currentMonthEnd])
                              ->orWhere('start_date', '<', $currentMonthStart);
                        });
                }

                if ($due == 'next') {
                    $nextMonthStart = Carbon::now()->addMonth()->startOfMonth()->format('Y-m-d');
                    $nextMonthEnd   = Carbon::now()->addMonth()->endOfMonth()->format('Y-m-d');

                    $data = $data->where('type', 2)
                        ->where('bill_paid', 0)
                        ->whereBetween('start_date', [$nextMonthStart, $nextMonthEnd]);
                }

                if ($due == 'next2') {
                    $next2MonthStart = Carbon::now()->addMonths(2)->startOfMonth()->format('Y-m-d');
                    $next2MonthEnd   = Carbon::now()->addMonths(2)->endOfMonth()->format('Y-m-d');

                    $data = $data->where('type', 2)
                        ->where('bill_paid', 0)
                        ->whereBetween('start_date', [$next2MonthStart, $next2MonthEnd]);
                }

                if ($due == 'next3') {
                    $next3MonthStart = Carbon::now()->addMonths(3)->startOfMonth()->format('Y-m-d');
                    $next3MonthEnd   = Carbon::now()->addMonths(3)->endOfMonth()->format('Y-m-d');

                    $data = $data->where('type', 2)
                        ->where('bill_paid', 0)
                        ->whereBetween('start_date', [$next3MonthStart, $next3MonthEnd]);
                }
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function($row) {
                    return '<input type="checkbox" class="row-checkbox" value="'.$row->id.'" data-client_id="'.$row->client_id.'">';
                })
                ->addColumn('start_date', function ($row) {
                    if ($row->type == 1) {
                        $firstRecord = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                            ->where('client_id', $row->client_id)
                            ->where('client_project_id', $row->client_project_id)
                            ->where('type', 1)
                            ->where('amount', $row->amount)
                            ->where('cycle_type', $row->cycle_type)
                            ->where('is_auto', $row->is_auto)
                            ->oldest('start_date')
                            ->first();
                        return $firstRecord->start_date ? Carbon::parse($firstRecord->start_date)->format('d-m-Y') : '';
                    }
                    return $row->start_date ? Carbon::parse($row->start_date)->format('d-m-Y') : '';
                })
                ->addColumn('end_date', function ($row) {
                    if (!$row->start_date) {
                        return '';
                    }

                    $date = Carbon::parse($row->start_date);

                    if ($row->cycle_type == 1) {
                        $date->addMonthNoOverflow();
                    } elseif ($row->cycle_type == 2) {
                        $date->addYear();
                    } else {
                        $date->addDay();
                    }

                    return $date->format('d-m-Y');
                })
                ->addColumn('due_date', function ($row) {
                    if ($row->bill_paid == 1) return '';
                    return $row->due_date ? Carbon::parse($row->due_date)->format('d-m-Y') : '';
                })
                ->addColumn('next_renewal', function($row) {
                    if ($row->is_auto == 1 && $row->next_start_date && $row->next_end_date) {
                        $cycle = $row->cycle_type == 1 ? 'Monthly' : ($row->cycle_type == 2 ? 'Yearly' : '');
                        return Carbon::parse($row->next_start_date)->format('d-m-Y') 
                            . ' - ' 
                            . Carbon::parse($row->next_end_date)->format('d-m-Y')
                            . ($cycle ? " ({$cycle})" : '');
                    }
                    return '';
                })
                ->addColumn('service_type', function($row) {
                    $typeText = $row->type == 1 ? 'In House' : 'Third Party';
                    return $row->serviceType?->name . ' (' . $typeText . ')';
                })
                ->addColumn('client_name', fn($row) => $row->client?->name)
                ->addColumn('project_title', fn($row) => $row->project?->title)
                ->addColumn('amount', function ($row) {
                    if ($row->bill_paid == 1) return '';
                    if ($row->type == 1) {
                        $totalAmount = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                            ->where('client_id', $row->client_id)
                            ->where('client_project_id', $row->client_project_id)
                            ->where('type', 1)
                            ->where('bill_paid', '!=', 1)
                            ->where('amount', $row->amount)
                            ->where('cycle_type', $row->cycle_type)
                            ->where('is_auto', $row->is_auto)
                            ->sum('amount');
                        $count = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                            ->where('client_id', $row->client_id)
                            ->where('client_project_id', $row->client_project_id)
                            ->where('type', 1)
                            ->where('bill_paid', '!=', 1)
                            ->where('amount', $row->amount)
                            ->where('cycle_type', $row->cycle_type)
                            ->where('is_auto', $row->is_auto)
                            ->count();

                        $cycleText = $row->cycle_type == 1 ? 'month' : 'year';
                        return '£' . number_format($row->amount, 0) . ' x ' . $count . ' ' . $cycleText . ' = £' . number_format($totalAmount, 0);
                    }
                    return '£' . number_format($row->amount, 0);
                })
                ->addColumn('note', fn($row) => \Str::limit(strip_tags($row->note), 100))
                ->addColumn('status', function($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="custom-control-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function($row) {
                    $btn = '';

                    if ($row->type == 2 && $row->is_renewed == 0 && $row->bill_paid == 1 && $row->status == 1) {
                        $start = Carbon::parse($row->start_date)->format('j F Y');
                        $end = Carbon::parse($row->end_date)->format('j F Y');

                        $btn .= '<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#renewModal'.$row->id.'">
                                    <i class="fas fa-sync"></i> Renew
                                </button>';

                        $btn .= '
                        <div class="modal fade" id="renewModal'.$row->id.'" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="POST" action="'.route('project-service.renew').'" class="renew-form">
                              '.csrf_field().'
                              <input type="hidden" name="service_id" value="'.$row->id.'">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">Renewal for period '.$start.' - '.$end.'</h5>
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                  <div class="mb-3">
                                    <label>Renewal Date</label>
                                    <input type="date" class="form-control" name="renewal_date" value="'.now()->format('Y-m-d').'" required>
                                  </div>
                                  <div class="mb-3">
                                    <label>Note</label>
                                    <textarea name="note" class="form-control"></textarea>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="submit" class="btn btn-primary">Renew</button>
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>';
                    } elseif ($row->type == 2 && $row->is_renewed == 1) {
                        // $btn .= '<button class="btn btn-sm btn-secondary" disabled>Renewed</button>';
                    }

                  // service details
                  $btn .= '<button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#billDetailsModal'.$row->id.'">
                              <i class="fas fa-list"></i>
                          </button>';

                  $btn .= '
                  <div class="modal fade" id="billDetailsModal'.$row->id.'" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Service Details</h5>
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                          <table class="table cell-border table-hover data-table">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Project</th>
                                <th>Duration</th>
                                <th>Payment Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Txn</th>
                                <th>Note</th>
                              </tr>
                            </thead>
                            <tbody>';

                  $bills = ProjectServiceDetail::with([
                          'transaction' => fn($q) => $q->where('transaction_type', 'Received'),
                          'client',
                          'serviceType',
                          'project',
                          'renewal'
                      ])
                      ->where('project_service_id', $row->project_service_id)
                      ->where('client_id', $row->client_id)
                      ->where('client_project_id', $row->client_project_id)
                      ->where('amount', $row->amount)
                      ->where('cycle_type', $row->cycle_type)
                      ->where('is_auto', $row->is_auto)
                      ->latest()
                      ->get();

                  foreach ($bills as $index => $bill) {
                      $duration = '';
                      if ($bill->start_date && $bill->end_date) {
                          $duration = Carbon::parse($bill->start_date)->format('d-m-y') . ' to ' .
                                      Carbon::parse($bill->end_date)->format('d-m-y');
                      }

                      $paymentDate = $bill->transaction?->date ? Carbon::parse($bill->transaction->date)->format('d-m-y') : '-';
                      $method = $bill->transaction?->payment_type ?? '-';
                      $txn = $bill->transaction?->tran_id ?? '-';
                      $note = $bill->transaction?->description ?? '-';

                      if ($bill->bill_paid) {
                          if ($bill->type == 2) {
                              if ($bill->renewal) {
                                  $status = '<span class="badge badge-success">Received</span>
                                            <br>
                                            <small class="text-info fst-italic d-block">
                                                Renewed: ' . ($bill->renewal->note ?? '') . ' - ' . Carbon::parse($bill->renewal->date)->format('j F Y') . '
                                            </small>';
                              } else {
                                  $status = '<span class="badge badge-success">Received</span>
                                            <span class="badge badge-danger d-block">Needs Renewal</span>';
                              }
                          } else {
                              $status = '<span class="badge badge-success">Received</span>';
                          }
                      } else {
                          $status = ($bill->due_date && Carbon::parse($bill->due_date)->lt(Carbon::today()))
                              ? '<span class="badge badge-danger">Overdue</span>'
                              : '<span class="badge badge-warning">Pending</span>';
                      }

                      $btn .= '<tr>
                                <td>'.($index + 1).'</td>
                                <td>'.$bill->client?->name.'</td>
                                <td>'.$bill->serviceType?->name.'</td>
                                <td>'.$bill->project?->title.'</td>
                                <td>'.$duration.'</td>
                                <td>'.$paymentDate.'</td>
                                <td>£'.number_format($bill->amount, 0).'</td>
                                <td>'.$method.'</td>
                                <td>'.$status.'</td>
                                <td>'.$txn.'</td>
                                <td>'.$note.'</td>
                              </tr>';
                  }

                  $btn .= '   </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>';


                  if ($row->isPending()) {
                      $isType1 = $row->type == 1;
                      $buttonText = $isType1 ? 'Receive' : 'Receive';
                      
                      $btn .= '<button class="btn btn-sm btn-success" data-toggle="modal" data-target="#receiveModal'.$row->id.'">'.$buttonText.'</button>';
                      if (auth()->user()->can('edit service')) {
                          $btn .= ' <button class="btn btn-sm btn-info edit" data-id="'.$row->id.'">Edit</button>';
                      }
                      $btn .= ' <button class="btn btn-sm btn-danger delete d-none" data-id="'.$row->id.'">Delete</button>';

                      // Modal
                      $btn .= '
                      <div class="modal fade" id="receiveModal'.$row->id.'" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                          <form method="POST" action="'.route('project-service.receive').'" class="receive-form">
                            '.csrf_field().'
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">'.$buttonText.' Payment</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                              </div>
                              <div class="modal-body">';
                      
                      if ($isType1) {
                          $bills = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                              ->where('client_id', $row->client_id)
                              ->where('client_project_id', $row->client_project_id)
                              ->where('type', 1)
                              ->where('bill_paid', '!=', 1)
                              ->where('amount', $row->amount)
                              ->where('cycle_type', $row->cycle_type)
                              ->where('is_auto', $row->is_auto)
                              ->orderBy('start_date')
                              ->get();
                          
                          $btn .= '<div class="mb-3">
                                      <label>Select Month(s) <span class="text-danger">*</span></label>
                                      <select name="bill_ids[]" class="form-control bill-select select2" multiple required>';
                          foreach ($bills as $bill) {
                              $btn .= '<option value="'.$bill->id.'" data-amount="'.$bill->amount.'">'.
                                      Carbon::parse($bill->start_date)->format('d-m-Y').' - '.
                                      Carbon::parse($bill->end_date)->format('d-m-Y').
                                      ' ( £'.number_format($bill->amount, 0).' )</option>';
                          }
                          $btn .= '</select></div>';
                          $btn .= '<div class="mb-3">
                                      <label>Total Amount</label>
                                      <input type="text" class="form-control total-amount" readonly>
                                  </div>';
                      } else {
                          $btn .= '<input type="hidden" name="bill_ids[]" value="'.$row->id.'">';
                          $btn .= '<div class="mb-3">
                                      <label>Total Amount</label>
                                      <input type="text" class="form-control total-amount" readonly value="£'.number_format($row->amount,2).'">
                                  </div>';
                      }

                      $btn .= '<div class="mb-3">
                                  <label>Payment Date <span class="text-danger">*</span></label>
                                  <input type="date" name="payment_date" class="form-control" value="'.now()->format('Y-m-d').'" required>
                              </div>';

                      $btn .= '<div class="mb-3">
                                  <label>Payment Type <span class="text-danger">*</span></label>
                                  <select name="payment_type" class="form-control" required>
                                      <option value="Cash">Cash</option>
                                      <option value="Bank">Bank</option>
                                  </select>
                              </div>
                              <div class="mb-3">
                                  <label>Note</label>
                                  <textarea name="note" class="form-control"></textarea>
                              </div>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" class="btn btn-success">'.$buttonText.'</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              </div>
                            </div>
                          </form>
                        </div>
                      </div>';
                  } else {
                      $disabledText = $row->type == 1 ? 'Received' : 'Received';
                      $btn .= '<button class="btn btn-sm btn-success" disabled>'.$disabledText.'</button>';
                  }

                  $serviceIds = $row->type == 1
                    ? ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                        ->where('client_id', $row->client_id)
                        ->where('client_project_id', $row->client_project_id)
                        ->where('type', 1)
                        ->where('bill_paid', '!=', 1)
                        ->where('amount', $row->amount)
                        ->where('cycle_type', $row->cycle_type)
                        ->where('is_auto', $row->is_auto)
                        ->orderBy('start_date')
                        ->pluck('id')
                        ->toArray()
                    : [$row->id];

                  $invoiceUrl = route('project-services.invoice.show') . '?service_ids=' . implode(',', $serviceIds);
                  $btn .= '<a href="'.$invoiceUrl.'" class="btn btn-sm btn-info mr-1" title="Invoice" target="_blank">
                              <i class="fas fa-file-invoice-dollar"></i> Invoice
                          </a>';
                  $emailUrl = route('client.email', ['id' => $row->client_id]) . '?service_ids=' . implode(',', $serviceIds);
                  $btn .= '<a href="'.$emailUrl.'" class="btn btn-sm btn-warning" title="Send Email">
                              <i class="fas fa-envelope"></i>
                          </a>';

                    return $btn;
                })
                ->rawColumns(['status', 'action', 'is_renewed', 'checkbox'])
                ->make(true);
        }

        $serviceTypes = ProjectService::where('status', 1)->latest()->get();
        $clients = Client::where('status', 1)->select('id', 'business_name')->latest()->get();
        $projects = ClientProject::select('id', 'title')->latest()->get();
        return view('admin.client-projects.services', compact('serviceTypes', 'clients', 'projects'));
    }

    private function checkAndGenerateServices()
    {
        $details = ProjectServiceDetail::where('status', 1)
            ->where(function($q) {
                $q->where(function($t1) { // In-house
                    $t1->where('type', 1)
                    ->where('status', 1)
                    ->where('next_created', 0);
                })
                ->orWhere(function($t2) { // Third-party
                    $t2->where('type', 2)
                    ->where('status', 1)
                    ->where('bill_paid', 1)
                    ->where('is_renewed', 1)
                    ->where('next_created', 0);
                });
            })
            ->where(function($q) {
                $q->where(function($m) {
                    $m->where('type', 1)
                      ->where('next_start_date', '<=', now()->format('Y-m-d'));
                })
                ->orWhere(function($y) {
                    $y->where('type', 2)
                      ->where('next_start_date', '<=', '2050-12-31');
                });
            })
            ->get();

        foreach ($details as $detail) {
            $newDetail = $detail->replicate();

            $startDate = Carbon::parse($detail->next_start_date)->format('Y-m-d');
            $endDate = Carbon::parse($detail->next_end_date)->format('Y-m-d');

            $dueDate = null;
            if ($detail->cycle_type == 1) {
                $dueDate = Carbon::parse($endDate)->subWeeks(2)->format('Y-m-d');
            } elseif ($detail->cycle_type == 2) {
                $dueDate = Carbon::parse($endDate)->subMonths(3)->format('Y-m-d');
            }
            $newDetail->due_date = $dueDate;

            $newDetail->start_date = $startDate;
            $newDetail->end_date = $endDate;
            $newDetail->next_created = 0;
            $newDetail->bill_paid = 0;
            $newDetail->is_renewed = 0;
            $newDetail->last_auto_run = now();
            $newDetail->created_at = now();
            $newDetail->updated_at = now();

            $nextStart = Carbon::parse($endDate)->addDay();
            if ($detail->cycle_type === 1) {
                $nextEnd = $nextStart->copy()->addMonthNoOverflow()->subDay();
            } else {
                $nextEnd = $nextStart->copy()->addYear()->subDay();
            }

            $newDetail->next_start_date = $nextStart->format('Y-m-d');
            $newDetail->next_end_date = $nextEnd->format('Y-m-d');
            $newDetail->save();

            $detail->next_created = 1;
            $detail->save();

            $serviceName = optional($newDetail->projectService)->name ?? 'Service';

            $transaction = new Transaction();
            $transaction->date = $startDate;
            $transaction->project_service_detail_id = $newDetail->id;
            $transaction->client_id = $newDetail->client_id;
            $transaction->table_type = 'Income';
            $transaction->transaction_type = 'Due';
            $transaction->payment_type = 'Bank';
            $transaction->description = $newDetail->note ?? "Due for {$serviceName} for service period {$startDate} to {$endDate}";
            $transaction->amount = $newDetail->amount;
            $transaction->at_amount = $newDetail->amount;
            $transaction->created_by = $newDetail->created_by;
            $transaction->save();

            $transaction->tran_id = 'AT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();

            Log::info("AutoCreate: Created new detail and transaction", [
                'detail_id'      => $newDetail->id,
                'transaction_id' => $transaction->id,
                'client_id'      => $newDetail->client_id,
                'period'         => "{$startDate} to {$endDate}",
            ]);
        }
    }

    public function invoice(Request $request)
    {
        $serviceIds = $request->query('service_ids')
            ? explode(',', $request->query('service_ids'))
            : [];

        if (empty($serviceIds)) {
            abort(404, 'No service IDs provided');
        }

        $services = ProjectServiceDetail::with(['serviceType', 'client'])
            ->whereIn('id', $serviceIds)
            ->get();

        if ($services->isEmpty()) {
            abort(404, 'No services found for invoice');
        }

        $company = CompanyDetails::first();

        return view('admin.client-projects.service_invoice', compact('services', 'company'));
    }

    public function sendMultiEmail(Request $request)
    {
      dd($request->all());
      return response()->json(['message' => 'Service invoice email sent successfully.']);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type_id' => 'required',
            'client_id' => 'required',
            'client_project_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|after_or_equal:start_date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'cycle_type' => 'nullable|in:1,2',
            'is_auto' => 'nullable|boolean',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $isAuto = $request->input('is_auto') == 1 ? 1 : 0;
        $cycleType = $request->input('cycle_type');

        $latest = ProjectServiceDetail::where([
            'project_service_id' => $request->service_type_id,
            'client_id' => $request->client_id,
            'client_project_id' => $request->client_project_id,
            'type' => $request->type,
            'is_auto' => $isAuto,
            'cycle_type' => $cycleType,
        ])
        ->orderByDesc('id')
        ->first();

        $exists = $latest && $latest->status == 1;

        if ($exists) {
            return response()->json([
                'status' => 422,
                'errors' => [
                    'duplicate' => ['An active record with the same details already exists.']
                ]
            ], 422);
        }

        $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
        $endDate   = Carbon::parse($request->end_date)->format('Y-m-d');

        $nextStartDate = Carbon::parse($endDate)->addDay()->format('Y-m-d');
        $nextEndDate = null;
        if ($cycleType == 1) {
            $nextEndDate = Carbon::parse($nextStartDate)->addMonthNoOverflow()->subDay()->format('Y-m-d');
        } elseif ($cycleType == 2) {
            $nextEndDate = Carbon::parse($nextStartDate)->addYear()->subDay()->format('Y-m-d');
        }

        $dueDate = null;
        if ($cycleType == 1) {
            $dueDate = Carbon::parse($endDate)->subWeeks(2)->format('Y-m-d');
        } elseif ($cycleType == 2) {
            $dueDate = Carbon::parse($endDate)->subMonths(3)->format('Y-m-d');
        }

        $detail = ProjectServiceDetail::create([
            'project_service_id' => $request->service_type_id,
            'client_id' => $request->client_id,
            'client_project_id' => $request->client_project_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'due_date' => $dueDate,
            'amount' => $request->amount,
            'note' => $request->note,
            'status' => true,
            'type' => $request->type,
            'is_auto' => $isAuto,
            'cycle_type' => $cycleType,
            'next_start_date' => $nextStartDate,
            'next_end_date' => $nextEndDate,
            'created_by' => auth()->id(),
        ]);
        
        $service = ProjectService::find($request->service_type_id);

        $transaction = new Transaction();
        $transaction->date = $startDate;
        $transaction->project_service_detail_id = $detail->id;
        $transaction->client_id = $request->client_id;
        $transaction->table_type = 'Income';
        $transaction->transaction_type = 'Due';
        $transaction->payment_type = 'Bank';
        $transaction->description = $detail->note ?? "Due for {$service->name} for service period ".$startDate." to ".$endDate;
        $transaction->amount = $detail->amount;
        $transaction->at_amount = $detail->amount;
        $transaction->created_by = auth()->id();
        $transaction->created_ip = $request->ip();
        $transaction->save();
        $transaction->tran_id = 'AT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        return response()->json([
            'status' => 200,
            'message' => 'Created successfully.',
            'data' => $detail
        ], 201);
    }

    public function edit(ProjectServiceDetail $service)
    {
        if (!$service) {
            return response()->json(['status' => 404, 'message' => 'Service not found'], 404);
        }
        return response()->json($service);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type_id' => 'required',
            'client_id' => 'required',
            'client_project_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'cycle_type' => 'nullable|in:1,2',
            'is_auto' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $isAuto = $request->input('is_auto') == 1 ? 1 : 0;
        $cycleType = $isAuto ? (int) $request->input('cycle_type', 2) : null;

        $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
        $endDate = Carbon::parse($request->end_date)->format('Y-m-d');

        $nextStartDate = null;
        $nextEndDate = null;

        if ($isAuto) {
            $nextStartDate = Carbon::parse($endDate)->addDay();

            if ($cycleType === 1) {
                $nextEndDate = $nextStartDate->copy()->addMonthNoOverflow()->subDay();
            } elseif ($cycleType === 2) {
                $nextEndDate = $nextStartDate->copy()->addYear()->subDay();
            }

            $nextStartDate = $nextStartDate->format('Y-m-d');
            $nextEndDate = $nextEndDate->format('Y-m-d');
        }

        $dueDate = null;

        if ($cycleType == 1) {
            $dueDate = Carbon::parse($endDate)->subWeeks(2)->format('Y-m-d');
        } elseif ($cycleType == 2) {
            $dueDate = Carbon::parse($endDate)->subMonths(3)->format('Y-m-d');
        }

        $detail = ProjectServiceDetail::find($request->codeid);
        $detail->project_service_id = $request->service_type_id;
        $detail->client_id = $request->client_id;
        $detail->client_project_id = $request->client_project_id;
        $detail->start_date = $startDate;
        $detail->end_date = $endDate;
        $detail->due_date = $dueDate;
        $detail->type = $request->type;
        $detail->amount = $request->amount;
        $detail->note = $request->note;
        $detail->is_auto = $isAuto;
        $detail->cycle_type = $request->cycle_type;
        $detail->next_start_date = $nextStartDate;
        $detail->next_end_date = $nextEndDate;
        $detail->updated_by = auth()->id();

        if ($detail->save()) {
           $service = ProjectService::find($request->service_type_id);
            $transaction = Transaction::where('project_service_detail_id', $detail->id)->first();
            if ($transaction) {
                $transaction->date = $startDate;
                $transaction->client_id = $request->client_id;
                $transaction->description = $detail->note;
                $transaction->amount = $detail->amount;
                $transaction->at_amount = $detail->amount;
                $transaction->updated_by = auth()->id();
                $transaction->updated_ip = $request->ip();
                $transaction->save();
            }

            return response()->json([
                'status' => 200,
                'message' => 'Updated successfully.',
                'data' => $detail
            ], 200);
        }
        return response()->json(['status' => 500, 'message' => 'Server error.'], 500);
    }

    public function destroy(ProjectServiceDetail $detail)
    {
        if (!$detail) {
            return response()->json(['success'=>false, 'message'=>'Detail not found'], 404);
        }

        $detail->transactions()->delete();
        
        if ($detail->delete()) {
            return response()->json(['success'=>true, 'message'=>'Detail deleted successfully']);
        }

        return response()->json(['success'=>false, 'message'=>'Failed to delete detail'], 500);
    }

    public function toggleStatus(Request $request, ProjectServiceDetail $detail)
    {
        if (!$detail) {
            return response()->json(['status'=>404, 'message'=>'Detail not found']);
        }

        $detail->status = $request->status;
        $detail->save();

        return response()->json(['status'=>200, 'message'=>'Status updated successfully']);
    }

    public function receive(Request $request)
    {
        $billIds = $request->bill_ids ?? [];
        $paymentType = $request->payment_type;
        $note = $request->note;
        $paymentDate = $request->payment_date ?? now()->format('Y-m-d');

        foreach ($billIds as $id) {
            $serviceDetail = ProjectServiceDetail::with('serviceType')->find($id);
            if (!$serviceDetail || $serviceDetail->bill_paid) continue;

            $previousTransaction = Transaction::where('project_service_detail_id', $id)
                ->where('transaction_type', 'Due')->first();

            $transaction = new Transaction();
            $transaction->date = $paymentDate;
            $transaction->project_service_detail_id = $id;
            $transaction->client_id = $previousTransaction->client_id ?? $serviceDetail->client_id;
            $transaction->table_type = 'Income';
            $transaction->transaction_type = 'Received';
            $transaction->payment_type = $paymentType;
            $transaction->description = $note ?? "Due paid for {$serviceDetail->serviceType->name} for service period ".
                Carbon::parse($serviceDetail->start_date)->format('d-m-Y').
                " to ".Carbon::parse($serviceDetail->end_date)->format('d-m-Y');
            $transaction->amount = $previousTransaction->amount ?? $serviceDetail->amount;
            $transaction->at_amount = $transaction->amount;
            $transaction->created_by = auth()->id();
            $transaction->created_ip = request()->ip();
            $transaction->save();

            $transaction->tran_id = 'AT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();

            $serviceDetail->bill_paid = 1;
            $serviceDetail->save();
        }

        return response()->json(['message' => 'Received successfully.']);
    }

    public function renew(Request $request)
    {
        $request->validate([
            'service_id'    => 'required|exists:project_service_details,id',
            'renewal_date'  => 'required|date',
            'note'          => 'nullable|string',
        ]);

        $serviceDetail = ProjectServiceDetail::findOrFail($request->service_id);

        if ($serviceDetail->is_renewed) {
            return response()->json(['message' => 'Service already renewed!'], 422);
        }

        $renewal = new ServiceRenewal();
        $renewal->project_service_detail_id = $serviceDetail->id;
        $renewal->date = $request->renewal_date;
        $renewal->note = $request->note;
        $renewal->status = 1;
        $renewal->created_by = auth()->id();
        $renewal->save();

        $serviceDetail->is_renewed = 1;
        $serviceDetail->save();

        return response()->json(['message' => 'Renewed successfully!']);
    }

    public function projects(Client $client)
    {
        return response()->json($client->projects()->select('id', 'title')->get());
    }
}