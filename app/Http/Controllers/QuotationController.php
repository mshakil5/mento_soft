<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Quotation::orderBy('status', 'asc')->orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('full_name', fn($row) => $row->first_name . ' ' . $row->last_name)
                ->addColumn('date', fn($row) => Carbon::parse($row->created_at)->format('d-m-Y'))
                ->addColumn('status', function($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="quotationStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="custom-control-label" for="quotationStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function($row) {
                    return '
                        <button class="btn btn-sm btn-info view" data-id="'.$row->id.'">View</button>
                        <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.quotations.index');
    }

    public function show($id)
    {
        $quotation = Quotation::find($id);
        if (!$quotation) return response()->json(['status' => 404, 'message' => 'Not found'], 404);

        $quotation->formatted_created_at = $quotation->created_at->format('d-m-Y | H:i:s');
        return response()->json($quotation);
    }

    public function destroy($id)
    {
        $quotation = Quotation::find($id);
        if (!$quotation) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        $quotation->delete();
        return response()->json(['success' => true, 'message' => 'Quotation deleted successfully']);
    }

    public function toggleStatus(Request $request)
    {
        $quotation = Quotation::find($request->quotation_id);
        if (!$quotation) return response()->json(['status' => 404, 'message' => 'Not found']);

        $quotation->status = $request->status;
        $quotation->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}