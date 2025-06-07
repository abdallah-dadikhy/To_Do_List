<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CategoryResource;


class CategoryController extends Controller
{
   
    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

 
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = Category::create($validator->validated());

    return response()->json([
        'message' => 'Category added successfully',
        'data' => new CategoryResource($category)
    ], 200);    }

    
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return new CategoryResource($category);
    }

    
    public function update(Request $request, $id)
{
    $category = Category::find($id);
    if (!$category) {
        return response()->json(['message' => 'Category not found'], 404);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'string|unique:categories,name,' . $id,
        'description' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $category->update($validator->validated());

    return response()->json([
        'message' => 'Category updated successfully',
        'data' => new CategoryResource($category)
    ], 200);
}


    
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'delete successfully'], 204);
    }
}
