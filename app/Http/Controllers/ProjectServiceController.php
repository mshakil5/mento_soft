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
use PDF;
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

            if ($request->service_id) {
                $data = $data->where('type', $request->service_id);
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
                    $today = Carbon::today()->format('Y-m-d');
                    $next30Days = Carbon::today()->addDays(30)->format('Y-m-d');

                    $data = $data->where('status', 1)
                        ->where('bill_paid', 0)
                        ->where(function($q) use ($today, $next30Days) {
                            $q->whereBetween('start_date', [$today, $next30Days]) // today + next 30 days
                              ->orWhere('start_date', '<', $today); // all previous unpaid
                        });
                }

                if ($due == 'next') {
                    $nextMonthStart = Carbon::now()->addMonth()->startOfMonth()->format('Y-m-d');
                    $nextMonthEnd   = Carbon::now()->addMonth()->endOfMonth()->format('Y-m-d');

                    $data = $data->where('type', 2)
                        ->where('status', 1)
                        ->where('bill_paid', 0)
                        ->whereBetween('start_date', [$nextMonthStart, $nextMonthEnd]);
                }

                if ($due == 'next2') {
                    $next2MonthStart = Carbon::now()->addMonths(2)->startOfMonth()->format('Y-m-d');
                    $next2MonthEnd   = Carbon::now()->addMonths(2)->endOfMonth()->format('Y-m-d');

                    $data = $data->where('type', 2)
                        ->where('status', 1)
                        ->where('bill_paid', 0)
                        ->whereBetween('start_date', [$next2MonthStart, $next2MonthEnd]);
                }

                if ($due == 'next3') {
                    $next3MonthStart = Carbon::now()->addMonths(3)->startOfMonth()->format('Y-m-d');
                    $next3MonthEnd   = Carbon::now()->addMonths(3)->endOfMonth()->format('Y-m-d');

                    $data = $data->where('type', 2)
                        ->where('status', 1)
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
                ->addColumn('first_start_date', function ($row) {
                    $firstRecord = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                        ->where('client_id', $row->client_id)
                        ->where('client_project_id', $row->client_project_id)
                        ->where('amount', $row->amount)
                        ->where('cycle_type', $row->cycle_type)
                        ->where('is_auto', $row->is_auto)
                        ->orderBy('id', 'asc')
                        ->first(['start_date', 'service_renewal_date']);

                    if (!$firstRecord) {
                        return '';
                    }

                    $html = \Carbon\Carbon::parse($firstRecord->start_date)->format('d-m-Y');

                    if ($firstRecord->service_renewal_date) {
                        $html .= '<br><span style="font-size: 12px; font-weight: normal;">' .
                                \Carbon\Carbon::parse($firstRecord->service_renewal_date)->format('d-m-Y') .
                                '</span>';
                    }

                    return $html;
                })
                ->addColumn('due_date', function ($row) {
                    if (!$row->start_date) {
                        return '';
                    }

                    $hasUnpaid = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                        ->where('client_id', $row->client_id)
                        ->where('client_project_id', $row->client_project_id)
                        ->where('amount', $row->amount)
                        ->where('cycle_type', $row->cycle_type)
                        ->where('is_auto', $row->is_auto)
                        ->where('bill_paid', 0)
                        ->exists();

                    if ($hasUnpaid) {
                        return Carbon::parse($row->start_date)->format('d-m-Y');
                    } else {
                        return '';
                    }
                })
                ->addColumn('service_type', function($row) {
                    $typeText = $row->type == 1 ? 'In House' : 'Third Party';
                    return $row->serviceType?->name . ' (' . $typeText . ')';
                })
                ->addColumn('client_name', fn($row) => $row->client?->name)
                ->addColumn('project_title', fn($row) => $row->project?->title)
                ->addColumn('amount', function ($row) {
                    // if ($row->bill_paid == 1) return '';
                    if ($row->type == 1 || $row->type == 2) {
                        $totalAmount = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                            ->where('client_id', $row->client_id)
                            ->where('client_project_id', $row->client_project_id)
                            //->where('type', 1)
                            ->where('bill_paid', '!=', 1)
                            ->where('amount', $row->amount)
                            ->where('cycle_type', $row->cycle_type)
                            ->where('is_auto', $row->is_auto)
                            ->sum('amount');
                        $count = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                            ->where('client_id', $row->client_id)
                            ->where('client_project_id', $row->client_project_id)
                            //->where('type', 1)
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

                    if ($row->type == 2 && $row->status == 1) {
                        $startDate = Carbon::parse($row->start_date);
                        $endDate = Carbon::parse($row->end_date);

                        if ($row->cycle_type == 1) { // 1 month cycle
                            $start = $startDate->copy()->addMonthNoOverflow()->format('j F Y');
                            $end = $endDate->copy()->addMonthNoOverflow()->format('j F Y');
                        } else { // 1 year cycle
                            $start = $startDate->copy()->addYear()->format('j F Y');
                            $end = $endDate->copy()->addYear()->format('j F Y');
                        }

                        if ($row->bill_paid == 1) {
                            $btnClass = 'btn-info';
                        } elseif (
                            ($row->cycle_type == 1 && now()->diffInDays(Carbon::parse($row->start_date), false) <= 10) ||
                            ($row->cycle_type == 2 && (Carbon::parse($row->start_date) < now() || now()->diffInMonths(Carbon::parse($row->start_date)) <= 3))
                        ) {
                            $btnClass = 'btn-danger';
                        } else {
                            $btnClass = 'btn-info';
                        }
                        $btn .= '<button class="btn btn-sm '.$btnClass.'" data-toggle="modal" data-target="#renewModal'.$row->id.'">
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

                    if ($row->type == 1 && $row->status == 1 && $row->bill_paid == 1) {
                        $startDate = Carbon::parse($row->start_date);
                        $endDate = Carbon::parse($row->end_date);
                        if ($row->cycle_type == 1) { // 1 month cycle
                            $start = $startDate->copy()->addMonthNoOverflow()->format('j F Y');
                            $end = $endDate->copy()->addMonthNoOverflow()->format('j F Y');
                        } else { // 1 year cycle
                            $start = $startDate->copy()->addYear()->format('j F Y');
                            $end = $endDate->copy()->addYear()->format('j F Y');
                        }

                        $btn .= '<button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#advanceReceiveModal'.$row->id.'">
                                  Advance Receive
                                </button>';

                        $btn .= '
                        <div class="modal fade" id="advanceReceiveModal'.$row->id.'" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog">
                            <form method="POST" action="'.route('project-service.advance-receive').'" class="advance-receive-form">
                              '.csrf_field().'
                              <input type="hidden" name="service_id" value="'.$row->id.'">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">Advance Receive for period '.$start.' - '.$end.'</h5>
                                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                  <div class="mb-3">
                                      <label>Advance Amount</label>
                                      <input type="number" class="form-control" name="advance_amount" value="'.$row->amount.'" required readonly>
                                  </div>
                                  <div class="mb-3">
                                      <label>Payment Date</label>
                                      <input type="date" class="form-control" name="payment_date" value="'.now()->format('Y-m-d').'" required>
                                  </div>
                                  <div class="mb-3">
                                      <label>Payment Type</label>
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
                                  <button type="submit" class="btn btn-primary">Receive</button>
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>';
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
                                <th class="d-none">#</th>
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
                                <th>Action</th>
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
                      ->orderBy('id', 'desc')
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
                                            <small class="text-info fst-italic d-none">
                                                Renewed: ' . ($bill->renewal->note ?? '') .'
                                            </small>';
                              } else {
                                
                                  $start = Carbon::parse($bill->start_date)->format('j F Y');
                                  $end = Carbon::parse($bill->end_date)->format('j F Y');
                                  $status = '<span class="badge badge-success">Received</span>
                                            <span class="badge badge-danger d-none">Needs Renewal</span>
                                             <button class="btn btn-sm btn-info d-none" data-toggle="modal" data-target="#renewModal'.$bill->id.'">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                <div class="modal fade" id="renewModal'.$bill->id.'" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="'.route('project-service.renew').'" class="renew-form">
                                        '.csrf_field().'
                                            <input type="hidden" name="service_id" value="'.$bill->id.'">
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
                              }
                          } else {
                              $status = '<span class="badge badge-success">Received</span>';
                          }
                      } else {
                          $status = ($bill->due_date && Carbon::parse($bill->due_date)->lt(Carbon::today()))
                              ? '<span class="badge badge-danger">Overdue</span>'
                              : '<span class="badge badge-warning">Due</span>';
                      }

                      $action = '';
                      if (!$bill->bill_paid) {
                          $action = '<button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editBill'.$bill->id.'">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger deleteBill"  id="'.$bill->id.'">
                                        <i class="fa fa-trash"></i>
                                    </button>
                          <div class="modal fade" id="editBill'.$bill->id.'" tabindex="-1" role="dialog" aria-hidden="true">
                              <div class="modal-dialog">
                                  <form method="POST" action="'.route('project-service-row.update').'" class="edit-form">
                                  '.csrf_field().'
                                  <input type="hidden" name="service_id" value="'.$bill->id.'">
                                      <div class="modal-content">
                                          <div class="modal-header">
                                              <h5 class="modal-title">Edit Service</h5>
                                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                                          </div>
                                          <div class="modal-body">
                                              <div class="mb-3">
                                                  <label>Start Date <span class="text-danger">*</span></label>
                                                  <input type="date" class="form-control" name="start_date" value="'.($bill->start_date ? \Carbon\Carbon::parse($bill->start_date)->format('Y-m-d') : '').'" required>
                                              </div>
                                              <div class="mb-3">
                                                  <label>Amount <span class="text-danger">*</span></label>
                                                  <input type="number" class="form-control" name="amount" value="'.$bill->amount.'" required>
                                              </div>
                                              <div class="mb-3">
                                                  <label>Note</label>
                                                  <textarea name="note" class="form-control">'.$bill->note.'</textarea>
                                              </div>
                                          </div>
                                          <div class="modal-footer">
                                              <button type="submit" class="btn btn-success">Update</button>
                                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                          </div>
                                      </div>
                                  </form>
                              </div>
                          </div>';
                      }

                      $btn .= '<tr>
                                <td class="d-none">'.$bill->id.'</td>
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
                                <td>'.$action.'</td>
                              </tr>';
                  }

                  $btn .= '   </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>';

                  $bills = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                      ->where('client_id', $row->client_id)
                      ->where('client_project_id', $row->client_project_id)
                      ->where('bill_paid', '!=', 1)
                      ->where('amount', $row->amount)
                      ->where('cycle_type', $row->cycle_type)
                      ->where('is_auto', $row->is_auto)
                      ->orderBy('start_date')
                      ->get();

                  if ($bills->count() > 0) {
                      $buttonText = 'Receive';

                      $hasUrgentBill = $bills->contains(function ($bill) {
                          return $bill->start_date < now() || now()->diffInDays($bill->start_date) <= 10;
                      });

                      $btnClass = $hasUrgentBill ? 'btn-danger' : 'btn-success';

                      $btn .= '<button class="btn btn-sm '.$btnClass.'" data-toggle="modal" data-target="#receiveModal'.$row->id.'"> '.$buttonText.' </button>';
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

                      if ($bills->count() > 0) {
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
                      } else {
                          $btn .= '<input type="hidden" name="bill_ids[]" value="'.$row->id.'">';
                      }

                      $btn .= '<div class="mb-3">
                                  <label>Total Amount</label>
                                  <input type="text" class="form-control total-amount" readonly>
                              </div>';

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
                      $btn .= '<button class="btn btn-sm btn-success" disabled>Received</button>';
                  }

                  if (auth()->user()->can('edit service')) {
                      $btn .= ' <button class="btn btn-sm btn-warning edit mr-1" data-id="'.$row->parent_id.'">Edit</button>';
                  }

                  $serviceIds = ProjectServiceDetail::where('project_service_id', $row->project_service_id)
                        ->where('client_id', $row->client_id)
                        ->where('client_project_id', $row->client_project_id)
                        ->where('type', $row->type)
                        ->where('bill_paid', '!=', 1)
                        ->where('amount', $row->amount)
                        ->where('cycle_type', $row->cycle_type)
                        ->where('is_auto', $row->is_auto)
                        ->orderBy('start_date')
                        ->pluck('id')
                        ->toArray();

                  if (count($serviceIds) > 0) {
                      $invoiceUrl = route('project-services.invoice.show') . '?service_ids=' . implode(',', $serviceIds);
                      $btn .= '<a href="'.$invoiceUrl.'" class="btn btn-sm btn-info mr-1" title="Invoice" target="_blank">
                                  <i class="fas fa-file-invoice-dollar"></i> Invoice
                              </a>';
                  } else {
                      $btn .= '<button class="btn btn-sm btn-info mr-1" title="Invoice" disabled>
                                  <i class="fas fa-file-invoice-dollar"></i> Invoice
                              </button>';
                  }
                  $emailUrl = route('client.email', ['id' => $row->client_id]) . '?service_ids=' . implode(',', $serviceIds);
                  $btn .= '<a href="'.$emailUrl.'" class="btn btn-sm btn-warning mr-1" title="Send Email">
                              <i class="fas fa-envelope"></i>
                          </a>';

                  $btn .= '<a href="'.route('service-invoices', ['id' => $row->parent_id]).'" class="btn btn-sm btn-secondary" title="Invoice">
                      <i class="fas fa-file-invoice"></i>
                  </a>';

                    return $btn;
                })
                ->rawColumns(['status', 'action', 'is_renewed', 'checkbox', 'first_start_date'])
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
                // In-house
                $q->where(function($t1) {
                    $t1->where('type', 1)
                      ->where('next_created', 0);
                })
                // Third-party
                ->orWhere(function($t2) {
                    $t2->where('type', 2)
                      ->where('bill_paid', 1)
                      // ->where('is_renewed', 1)
                      ->where('next_created', 0);
                });
            })
            ->get()
            ->filter(function($detail) {
                $end = Carbon::parse($detail->end_date ?? now());
                return match($detail->cycle_type) {
                    1 => $end->subWeeks(2)->lte(now()),   // monthly → 2 weeks before end
                    2 => $end->subMonths(3)->lte(now()),  // yearly → 3 months before end
                    default => false,
                };
            });

        foreach ($details as $detail) {
            $newDetail = $detail->replicate();
            $newDetail->parent_id = $detail->parent_id;

            $newStart = Carbon::parse($detail->end_date)->addDay();
            $newEnd = $detail->cycle_type === 1
                ? $newStart->copy()->addMonthNoOverflow()->subDay()
                : ($detail->cycle_type === 2
                    ? $newStart->copy()->addYear()->subDay()
                    : $detail->end_date);

            $startDate = $newStart->format('Y-m-d');
            $endDate   = $newEnd->format('Y-m-d');

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
            $newDetail->save();

            $detail->next_created = 1;
            $detail->save();

            $serviceName = optional($newDetail->projectService)->name ?? 'Service';

            $transaction = new Transaction();
            $transaction->date = $startDate;
            $transaction->project_service_detail_id = $newDetail->id;
            $transaction->client_id = $newDetail->client_id;
            $transaction->client_project_id = $newDetail->client_project_id;
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

    public function invoiceDownload(Request $request)
    {
        $invId = $request->query('invoice_number');

        $services = ProjectServiceDetail::with(['serviceType', 'client','receivedTransactions'])
            ->where('invoice_number', $invId)
            ->get();


        if ($services->isEmpty()) {
            abort(404, 'Invoice not found.');
        }

        $company = CompanyDetails::first();
        $logoPath = public_path('images/company/' . ($company->company_logo ?? ''));
        if (file_exists($logoPath) && is_readable($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $company->logo_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            $company->logo_base64 = null;
        }

        $paidImagePath = public_path('paidbg.png');
        if (file_exists($paidImagePath)) {
            $paidImageData = base64_encode(file_get_contents($paidImagePath));
            $paidImageBase64 = 'data:image/png;base64,' . $paidImageData;
        } else {
            $paidImageBase64 = null;
        }

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
        ])->loadView('admin.client-projects.service_invoice_download', compact('services', 'company', 'paidImageBase64','invId'));

        $fileName = 'invoice_' . $invId . '.pdf';
        return $pdf->download($fileName);
    }

    public function sendMultiEmail(Request $request)
    {
      dd($request->all());
      return response()->json(['message' => 'Service invoice email sent successfully.']);
    }

    public function store2(Request $request)
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
        if ($cycleType == 1) { // monthly
            $endDate = Carbon::parse($startDate)->addMonthNoOverflow()->format('Y-m-d');
        } elseif ($cycleType == 2) { // yearly
            $endDate = Carbon::parse($startDate)->addYear()->format('Y-m-d');
        }

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

        $detail->parent_id = $detail->id;
        $detail->save();

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type_id'       => 'required|exists:project_services,id',
            'client_id'             => 'required|exists:clients,id',
            'client_project_id'     => 'required|exists:client_projects,id',
            'start_date'            => 'required|date',
            'service_renewal_date'  => 'nullable|date',
            'amount'                => 'required|numeric|min:0',
            'note'                  => 'nullable|string',
            'cycle_type'            => 'required|in:1,2', // 1=Monthly, 2=Yearly
            'is_auto'               => 'nullable|boolean',
            'type'                  => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $isAuto = $request->boolean('is_auto');
        $cycleType = (int) $data['cycle_type'];
        $service = ProjectService::findOrFail($data['service_type_id']);
        $today = Carbon::today();
        $startDate = Carbon::parse($data['start_date'])->startOfDay();

        $createdDetails = [];
        $parentId = null;

        DB::transaction(function () use ($data, $isAuto, $cycleType, $service, $today, $startDate, $request, &$createdDetails, &$parentId) {

            $currentStart = $startDate;

            if ($currentStart->greaterThan($today)) {
                $currentEnd = $cycleType === 1
                    ? $currentStart->copy()->addMonthNoOverflow()->subDay()   
                    : $currentStart->copy()->addYear()->subDay();            

                $dueDate = $cycleType === 1
                    ? $currentEnd->copy()->subWeeks(2)
                    : $currentEnd->copy()->subMonths(3);

                $detail = ProjectServiceDetail::create([
                    'project_service_id'    => $data['service_type_id'],
                    'client_id'             => $data['client_id'],
                    'client_project_id'     => $data['client_project_id'],
                    'service_renewal_date'  => $data['service_renewal_date'] ?? null,
                    'start_date'            => $currentStart,
                    'end_date'              => $currentEnd,
                    'due_date'              => $dueDate,
                    'amount'                => $data['amount'],
                    'note'                  => $data['note'] ?? null,
                    'status'                => true,
                    'type'                  => $data['type'],
                    'is_auto'               => $isAuto,
                    'cycle_type'            => $cycleType,
                    'created_by'            => auth()->id(),
                ]);

                $parentId = $detail->id;
                $detail->update(['parent_id' => $parentId]);

                $transaction = Transaction::create([
                    'date'                      => $currentStart,
                    'project_service_detail_id' => $detail->id,
                    'client_project_id'         => $detail->client_project_id,
                    'client_id'                 => $data['client_id'],
                    'table_type'                => 'Income',
                    'transaction_type'          => 'Due',
                    'payment_type'              => 'Bank',
                    'description'               => $detail->note
                        ?? "Due for {$service->name} from {$currentStart->toDateString()} to {$currentEnd->toDateString()}",
                    'amount'       => $detail->amount,
                    'at_amount'    => $detail->amount,
                    'created_by'   => auth()->id(),
                    'created_ip'   => $request->ip(),
                ]);

                $transaction->update([
                    'tran_id' => 'AT' . now()->format('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT)
                ]);

                $createdDetails[] = $detail;
            }

            else {
                while ($currentStart->lessThanOrEqualTo($today)) {

                    $currentEnd = $cycleType === 1
                        ? $currentStart->copy()->addMonthNoOverflow()->subDay() 
                        : $currentStart->copy()->addYear()->subDay();         

                    $dueDate = $cycleType === 1
                        ? $currentEnd->copy()->subWeeks(2)
                        : $currentEnd->copy()->subMonths(3);

                      $isLast = $currentEnd->greaterThanOrEqualTo($today);

                    $detail = ProjectServiceDetail::create([
                        'project_service_id'    => $data['service_type_id'],
                        'client_id'             => $data['client_id'],
                        'client_project_id'     => $data['client_project_id'],
                        'service_renewal_date'  => $data['service_renewal_date'] ?? null,
                        'start_date'            => $currentStart,
                        'end_date'              => $currentEnd,
                        'due_date'              => $dueDate,
                        'amount'                => $data['amount'],
                        'note'                  => $data['note'] ?? null,
                        'status'                => true,
                        'next_created' => $isLast ? 0 : 1,
                        'type'                  => $data['type'],
                        'is_auto'               => $isAuto,
                        'cycle_type'            => $cycleType,
                        'created_by'            => auth()->id(),
                    ]);

                    if ($parentId === null) {
                        $parentId = $detail->id;
                    }
                    $detail->update(['parent_id' => $parentId]);

                    $transaction = Transaction::create([
                        'date'                      => $currentStart,
                        'project_service_detail_id' => $detail->id,
                        'client_project_id'         => $detail->client_project_id,
                        'client_id'                 => $data['client_id'],
                        'table_type'                => 'Income',
                        'transaction_type'          => 'Due',
                        'payment_type'              => 'Bank',
                        'description'               => $detail->note
                            ?? "Due for {$service->name} from {$currentStart->toDateString()} to {$currentEnd->toDateString()}",
                        'amount'       => $detail->amount,
                        'at_amount'    => $detail->amount,
                        'created_by'   => auth()->id(),
                        'created_ip'   => $request->ip(),
                    ]);

                    $transaction->update([
                        'tran_id' => 'AT' . now()->format('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT)
                    ]);

                    $createdDetails[] = $detail;

                    $currentStart = $cycleType === 1
                        ? $currentStart->copy()->addMonthNoOverflow()->startOfDay()
                        : $currentStart->copy()->addYear()->startOfDay();
                }
            }
        });

        return response()->json([
                'status'    => 201,
                'message'   => 'Created successfully.',
                'parent_id' => $parentId,
                'count'     => count($createdDetails),
                'data'      => $createdDetails
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
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'cycle_type' => 'nullable|in:1,2',
            'service_renewal_date'  => 'nullable|date',
            'is_auto' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $detail = ProjectServiceDetail::find($request->codeid);
        if (!$detail) {
            return response()->json(['status' => 404, 'message' => 'Record not found.'], 404);
        }

        $detail->project_service_id = $request->service_type_id;
        $detail->client_id = $request->client_id;
        $detail->client_project_id = $request->client_project_id;
        $detail->service_renewal_date = $request->service_renewal_date;
        $detail->type = $request->type;
        $detail->note = $request->note;
        $detail->updated_by = auth()->id();

        if($detail->bill_paid == 0) {
          $detail->amount = $request->amount;
        }
        if ($detail->save()) {

          $children = ProjectServiceDetail::where('parent_id', $detail->id)
              ->where('bill_paid', 0)
              ->get();

          foreach ($children as $child) {
              $child->project_service_id = $request->service_type_id;
              $child->client_id = $request->client_id;
              $child->client_project_id = $request->client_project_id;
              $child->amount = $request->amount;
              $child->note = $request->note;
              $child->type = $request->type;
              $child->updated_by = auth()->id();
              $child->save();

              $transactions = Transaction::where('project_service_detail_id', $child->id)->get();
              foreach ($transactions as $transaction) {
                  $transaction->client_id = $request->client_id;
                  $transaction->client_project_id = $request->client_project_id;
                  $transaction->description = $request->note ?? '';
                  $transaction->amount = $child->amount;
                  $transaction->at_amount = $child->amount;
                  $transaction->updated_by = auth()->id();
                  $transaction->updated_ip = $request->ip();
                  $transaction->save();
              }
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
            $transaction->client_project_id = $previousTransaction->client_project_id;
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

    public function renew2(Request $request)
    {
        $request->validate([
            'service_id'    => 'required|exists:project_service_details,id',
            'renewal_date'  => 'required|date',
            'note'          => 'nullable|string',
        ]);

        

        $serviceDetail = ProjectServiceDetail::findOrFail($request->service_id);
        $parentService = ProjectServiceDetail::find($serviceDetail->parent_id);
        // repeat based on $parentService last data. take necessary date from last dat and tak only amount from first data


        $renewal = new ServiceRenewal();
        $renewal->project_service_detail_id = $serviceDetail->id;
        $renewal->date = $request->renewal_date;
        $renewal->note = $request->note;
        $renewal->status = 1;
        $renewal->created_by = auth()->id();
        if ($serviceDetail->is_renewed != 1) {
        $renewal->save();
        }


        $serviceDetail->is_renewed = 1;
        $serviceDetail->save();

        return response()->json(['message' => 'Renewed successfully!']);
    }

    public function renew(Request $request)
    {
        $request->validate([
            'service_id'    => 'required|exists:project_service_details,id',
            'renewal_date'  => 'required|date',
            'note'          => 'nullable|string',
        ]);

        $serviceDetail = ProjectServiceDetail::findOrFail($request->service_id);

        $parentService = ProjectServiceDetail::find($serviceDetail->parent_id ?? $serviceDetail->id);

        $lastDetail = ProjectServiceDetail::where('parent_id', $parentService->id)
            ->orderByDesc('end_date')
            ->first();

        if (!$lastDetail) {
            $lastDetail = $parentService;
        }

        $newStart = Carbon::parse($lastDetail->end_date)->addDay()->startOfDay();

        if ($parentService->cycle_type == 1) {
            $newEnd = $newStart->copy()->endOfMonth();
            $dueDate = $newEnd->copy()->subWeeks(2);
        } else {
            $newEnd = $newStart->copy()->addYear()->subDay();
            $dueDate = $newEnd->copy()->subMonths(3);
        }

        DB::transaction(function () use ($request, $serviceDetail, $parentService, $lastDetail, $newStart, $newEnd, $dueDate) {
            $renewal = new ServiceRenewal();
            $renewal->project_service_detail_id = $serviceDetail->id;
            $renewal->date = $request->renewal_date;
            $renewal->note = $request->note;
            $renewal->status = 1;
            $renewal->created_by = auth()->id();

            if ($serviceDetail->is_renewed != 1) {
                $renewal->save();
            }

            $serviceDetail->update(['is_renewed' => 1, 'next_created' => 1]);

            $newDetail = ProjectServiceDetail::create([
                'project_service_id' => $parentService->project_service_id,
                'client_id'          => $parentService->client_id,
                'client_project_id'  => $parentService->client_project_id,
                'start_date'         => $newStart,
                'end_date'           => $newEnd,
                'due_date'           => $dueDate,
                'amount'             => $parentService->amount, // always from first row
                'note'               => $request->note ?? null,
                'status'             => true,
                'type'               => $parentService->type,
                'is_auto'            => $parentService->is_auto,
                'cycle_type'         => $parentService->cycle_type,
                'parent_id'          => $parentService->id, // keep same parent id
                'created_by'         => auth()->id(),
            ]);

            $transaction = Transaction::create([
                'date'                      => $newStart,
                'project_service_detail_id' => $newDetail->id,
                'client_id'                 => $parentService->client_id,
                'client_project_id'         => $parentService->client_project_id,
                'table_type'                => 'Income',
                'transaction_type'          => 'Due',
                'payment_type'              => 'Bank',
                'description'               => $request->note
                    ?? "Due for service renewal from {$newStart->toDateString()} to {$newEnd->toDateString()}",
                'amount'       => $newDetail->amount,
                'at_amount'    => $newDetail->amount,
                'created_by'   => auth()->id(),
                'created_ip'   => $request->ip(),
            ]);

            $transaction->update([
                'tran_id' => 'AT' . now()->format('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT)
            ]);
        });

        return response()->json(['message' => 'Renewed successfully!']);
    }

    public function advanceReceive(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:project_service_details,id',
            'advance_amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_type' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $serviceDetail = ProjectServiceDetail::with('serviceType')->find($request->service_id);
        if (!$serviceDetail) {
            return response()->json(['message' => 'Invalid service.'], 422);
        }

        $newDetail = $serviceDetail->replicate();
        $newDetail->parent_id = $serviceDetail->id;

        $startDate = Carbon::parse($serviceDetail->end_date)->addDay()->format('Y-m-d');

        if ($serviceDetail->cycle_type == 1) {
            $endDate = Carbon::parse($startDate)->copy()->addMonthNoOverflow()->subDay()->format('Y-m-d');
        } else {
            $endDate = Carbon::parse($startDate)->copy()->addYear()->subDay()->format('Y-m-d');
        }

        $dueDate = $serviceDetail->cycle_type == 1 ? Carbon::parse($endDate)->subWeeks(2)->format('Y-m-d') : Carbon::parse($endDate)->subMonths(3)->format('Y-m-d');

        $newDetail->start_date = $startDate;
        $newDetail->end_date = $endDate;
        $newDetail->due_date = $dueDate;
        $newDetail->next_created = 0;
        $newDetail->bill_paid = 1;
        $newDetail->is_renewed = 0;
        $newDetail->save();

        $dueTransaction = new Transaction();
        $dueTransaction->date = $startDate;
        $dueTransaction->project_service_detail_id = $newDetail->id;
        $dueTransaction->client_id = $newDetail->client_id;
        $dueTransaction->client_project_id = $newDetail->client_project_id;
        $dueTransaction->table_type = 'Income';
        $dueTransaction->transaction_type = 'Due';
        $dueTransaction->payment_type = 'Bank';
        $dueTransaction->description = $newDetail->note ?? "Due for {$serviceDetail->serviceType->name} for period {$startDate} to {$endDate}";
        $dueTransaction->amount = $newDetail->amount;
        $dueTransaction->at_amount = $newDetail->amount;
        $dueTransaction->created_by = $newDetail->created_by;
        $dueTransaction->save();
        $dueTransaction->tran_id = 'AT' . date('ymd') . str_pad($dueTransaction->id, 4, '0', STR_PAD_LEFT);
        $dueTransaction->save();

        $receivedTransaction = new Transaction();
        $receivedTransaction->date = $request->payment_date;
        $receivedTransaction->project_service_detail_id = $newDetail->id;
        $receivedTransaction->client_id = $serviceDetail->client_id;
        $receivedTransaction->client_project_id = $serviceDetail->client_project_id;
        $receivedTransaction->table_type = 'Income';
        $receivedTransaction->transaction_type = 'Received';
        $receivedTransaction->payment_type = $request->payment_type;
        $receivedTransaction->description = $request->note ?? "Advance payment for {$serviceDetail->serviceType->name} for period ". Carbon::parse($serviceDetail->start_date)->format('d-m-Y').' to '. Carbon::parse($serviceDetail->end_date)->format('d-m-Y');
        $receivedTransaction->amount = $request->advance_amount;
        $receivedTransaction->at_amount = $request->advance_amount;
        $receivedTransaction->created_by = auth()->id();
        $receivedTransaction->created_ip = $request->ip();
        $receivedTransaction->save();
        $receivedTransaction->tran_id = 'AT' . date('ymd') . str_pad($receivedTransaction->id, 4, '0', STR_PAD_LEFT);
        $receivedTransaction->save();

        return response()->json(['message' => 'Advance payment received successfully.']);
    }

    public function projects(Client $client)
    {
        return response()->json($client->projects()->select('id', 'title')->get());
    }

    public function updateRow(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:project_service_details,id',
            'start_date' => 'required|date',
            'amount'     => 'required|numeric|min:0',
            'note'       => 'nullable|string',
        ]);

        $detail = ProjectServiceDetail::findOrFail($request->service_id);

        if ($detail->bill_paid) {
            return response()->json(['message' => 'Cannot update a paid bill!'], 422);
        }

        $startDate = Carbon::parse($request->start_date);

        if ($detail->cycle_type == 1) { // monthly
            $endDate = $startDate->copy()->addMonthNoOverflow()->subDay();
            $dueDate = $endDate->copy()->subWeeks(2);
        } else { // yearly
            $endDate = $startDate->copy()->addYear()->subDay();
            $dueDate = $endDate->copy()->subMonths(3);
        }

        $nextStart = $endDate->copy()->addDay();
        if ($detail->cycle_type == 1) {
            $nextEnd = $nextStart->copy()->addMonthNoOverflow()->subDay();
        } else {
            $nextEnd = $nextStart->copy()->addYear()->subDay();
        }

        $detail->start_date      = $startDate->format('Y-m-d');
        $detail->end_date        = $endDate->format('Y-m-d');
        $detail->due_date        = $dueDate->format('Y-m-d');
        $detail->amount          = $request->amount;
        $detail->note            = $request->note;
        $detail->save();

        $transaction = $detail->transaction()->where('transaction_type', 'Due')->first();
        if ($transaction) {
            $transaction->date        = $startDate->format('Y-m-d');
            $transaction->amount      = $request->amount;
            $transaction->at_amount   = $request->amount;
            $transaction->description = $request->note ?? $transaction->description;
            $transaction->save();
        }

        return response()->json(['message' => 'Service updated successfully!']);
    }

    public function serviceInvoices(Request $request, $rowId)
    {
        $row = ProjectServiceDetail::findOrFail($rowId);

        $info = ($row->serviceType?->name ?? '-') 
                . ' | ' . ($row->project?->title ?? '-') 
                . ' | ' . ($row->client?->business_name ?? '-');

          $receivedTransactions = Transaction::whereHas('service', function($q) use ($row) {
                  $q->where('parent_id', $row->id)
                    ->whereNull('invoice_number');
              })
              ->where('transaction_type', 'Received')
              ->groupBy('date')
              ->select('date')
              ->get();

        if ($request->ajax()) {
            $bills = ProjectServiceDetail::with(['transaction' => fn($q) => $q->where('transaction_type', 'Received')])
                ->where('project_service_id', $row->project_service_id)
                ->where('client_id', $row->client_id)
                ->where('client_project_id', $row->client_project_id)
                ->where('amount', $row->amount)
                ->orderBy('start_date')
                ->whereNotNull('invoice_number')
                ->get();

            // Group by invoice_number
            $grouped = $bills->groupBy('invoice_number');

            return DataTables::of($grouped->map(function ($items, $invoice) {
                    $serviceIds = $items->pluck('id')->toArray();
                    $invoiceUrl = route('project-services.invoice.download') . '?invoice_number=' . $invoice;

                    return [
                        'invoice'      => '<a href="'.$invoiceUrl.'" class="btn btn-sm btn-info mr-1" title="Invoice" target="_blank">
                                            <i class="fas fa-file-invoice-dollar"></i> '.$invoice.'
                                        </a>',
                        'amount'       => '£'.number_format($items->sum('amount'), 0),
                        'payment_date' => $items->first()->transaction
                                            ? Carbon::parse($items->first()->transaction->date)->format('d-m-Y')
                                            : '-',
                        'method'       => $items->first()->transaction?->payment_type ?? '-',
                    ];
                }))
                ->addIndexColumn()
                ->rawColumns(['invoice'])
                ->make(true);


        }

        return view('admin.client-projects.project_service_invoices', compact('row', 'info', 'receivedTransactions'));
    }

    public function createServiceInvoice(Request $request)
    {
        $request->validate([
            'service_id' => 'required',
            'payment_date' => 'required|date',
        ]);

        $serviceId = $request->service_id;
        $date = Carbon::parse($request->payment_date)->format('Y-m-d');

        $invoiceNumber = 'MSS-' . mt_rand(100000, 999999);

        $serviceDetailIds = ProjectServiceDetail::where('parent_id', $serviceId)
            ->where('bill_paid', 1)
            ->whereNull('invoice_number')
            ->whereHas('transactions', function($q) use ($date) {
                $q->where('transaction_type', 'Received')
                  ->whereDate('date', $date);
            })
            ->pluck('id');

        ProjectServiceDetail::whereIn('id', $serviceDetailIds)
            ->where('bill_paid', 1)
            ->whereNull('invoice_number')
            ->update(['invoice_number' => $invoiceNumber]);

        return response()->json(['message' => 'Invoice created successfully!', 'invoice_number' => $invoiceNumber]);
    }
}