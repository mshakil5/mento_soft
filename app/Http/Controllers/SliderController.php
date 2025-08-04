<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slider;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    public function getSlider(Request $request)
    {
        if ($request->ajax()) {
            $data = Slider::latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        return '<a href="' . asset("images/slider/" . $row->image) . '" target="_blank">
                                    <img src="' . asset("images/slider/" . $row->image) . '" style="max-width:100px; height:auto;">
                                </a>';
                    }
                    return '';
                })
                ->addColumn('title', function ($row) {
                    return $row->title ?? '';
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus' . $row->id . '" data-id="' . $row->id . '" ' . $checked . '>
                                <label class="custom-control-label" for="customSwitchStatus' . $row->id . '"></label>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-info edit" data-id="' . $row->id . '">Edit</button>
                        <button class="btn btn-sm btn-danger delete" data-id="' . $row->id . '">Delete</button>
                    ';
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        return view('admin.slider.index');
    }

    public function sliderStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $data = new Slider;
        $data->title = $request->title;
        $data->sub_title = $request->sub_title;
        $data->link = $request->link;
        $data->slug = Str::slug($request->title);
        $data->created_by = auth()->id(); 

        $uploadedFile = $request->file('image');
        $randomName = mt_rand(10000000, 99999999) . '.webp';
        $destinationPath = public_path('images/slider/');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        Image::make($uploadedFile)
            ->resize(1600, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('webp', 50)
            ->save($destinationPath . $randomName);

        $data->image = $randomName;

        if ($data->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Slider created successfully.'
            ], 201);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function sliderEdit($id)
    {
        return response()->json(Slider::find($id));
    }

    public function sliderUpdate(Request $request)
    {
        $slider = Slider::find($request->codeid);
        $slider->title = $request->title;
        $slider->sub_title = $request->sub_title;
        $slider->link = $request->link;
        $slider->updated_by = auth()->id();

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');

            if ($slider->image && file_exists(public_path('images/slider/' . $slider->image))) {
                unlink(public_path('images/slider/' . $slider->image));
            }

            $randomName = mt_rand(10000000, 99999999) . '.webp';
            $destinationPath = public_path('images/slider/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            Image::make($uploadedFile)
              ->resize(1600, null, function ($constraint) {
                  $constraint->aspectRatio();
                  $constraint->upsize();
              })
              ->encode('webp', 50)
              ->save($destinationPath . $randomName);
            $slider->image = $randomName;
        }

        if ($slider->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Slider created successfully.'
            ], 201);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Server error.'
            ], 500);
        }
    }

    public function sliderDelete($id)
    {
        $slider = Slider::find($id);
        
        if (!$slider) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        if ($slider->image && file_exists(public_path('images/slider/' . $slider->image))) {
            unlink(public_path('images/slider/' . $slider->image));
        }

        if ($slider->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        $brand = Slider::find($request->brand_id);
        if (!$brand) {
            return response()->json(['status' => 404, 'message' => 'Brand not found']);
        }

        $brand->status = $request->status;
        $brand->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}
