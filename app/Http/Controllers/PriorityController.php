<?php

namespace App\Http\Controllers;

use App\Models\Priority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PriorityResource;


class PriorityController extends Controller
{

    public function index()
    {
        return PriorityResource::collection(Priority::all());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:priorities,name',
            'level' => 'required|integer|min:0|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $priority = Priority::create($validator->validated());

        return response()->json([
            'message' => 'Category added successfully',
            'data' => new PriorityResource($priority)
        ], 200);    
}

    public function update(Request $request, $id)
    {
        $priority = Priority::find($id);
        if (!$priority) {
            return response()->json(['message' => 'Priority not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:priorities,name,' . $id,
            'level' => 'required|integer|min:0|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $priority->update($validator->validated());

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => new PriorityResource($priority)
        ], 200);     
     }

    public function destroy($id)
    {
        $priority = Priority::find($id);
        if (!$priority) {
            return response()->json(['message' => 'Priority not found'], 404);
        }

        $priority->delete();
            return response()->json(['message' => 'Priority delete succesfully'], 204);
        }
}
