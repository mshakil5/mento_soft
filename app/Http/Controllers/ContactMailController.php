<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactEmail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ContactMailController extends Controller
{
    public function getContactEmail(Request $request)
    {
        if ($request->ajax()) {
            $data = ContactEmail::latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('email', function ($row) {
                    return $row->email ?? '';
                })
                ->addColumn('email_holder', function ($row) {
                    return $row->email_holder ?? '';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-info edit" data-id="' . $row->id . '">Edit</button>
                        <button class="btn btn-sm btn-danger delete" data-id="' . $row->id . '">Delete</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.contact_email.index');
    }

    public function contactEmailStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:contact_emails,email',
            'email_holder' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $data = new ContactEmail;
        $data->email = $request->email;
        $data->email_holder = $request->email_holder;
        $data->created_by = auth()->id();

        if ($data->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Contact email created successfully.'
            ], 201);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function contactEmailEdit($id)
    {
        return response()->json(ContactEmail::find($id));
    }

    public function contactEmailUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:contact_emails,email,'.$request->codeid,
            'email_holder' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $data = ContactEmail::find($request->codeid);
        $data->email = $request->email;
        $data->email_holder = $request->email_holder;
        $data->updated_by = auth()->id();

        if ($data->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Contact email updated successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function contactEmailDelete($id)
    {
        $contactEmail = ContactEmail::find($id);
        
        if (!$contactEmail) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        if ($contactEmail->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }
}