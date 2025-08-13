<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Contact::with('product')->orderBy('status', 'asc')->orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('full_name', function($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->addColumn('product', function($row) {
                    return $row->product ? $row->product->title : '';
                })
                ->addColumn('date', function($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y');
                })
                ->addColumn('status', function($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="custom-control-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function($row) {
                    return '
                      <button class="btn btn-sm btn-info view" data-id="'.$row->id.'">View</button>
                      <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.contacts.index');
    }

    public function show($id)
    {
        $contact = Contact::with('product')->find($id);
        if (!$contact) {
            return response()->json([
                'status' => 404,
                'message' => 'Contact not found'
            ], 404);
        }
        $contact->formatted_created_at = $contact->created_at->format('d-m-Y | H:i:s');
        $contact->product_title = $contact->product ? $contact->product->title : '';
        return response()->json($contact);
    }

    public function destroy($id)
    {
        $contact = Contact::find($id);
        
        if (!$contact) {
            return response()->json(['success' => false, 'message' => 'Contact not found.'], 404);
        }

        if ($contact->delete()) {
            return response()->json(['success' => true, 'message' => 'Contact deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete contact.'], 500);
    }

    public function toggleStatus(Request $request)
    {
        $contact = Contact::find($request->contact_id);
        if (!$contact) {
            return response()->json(['status' => 404, 'message' => 'Contact not found']);
        }

        $contact->status = $request->status;
        $contact->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}