<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->sort ?? 'created_at';
        $order = $request->order ?? 'desc';

        $categories = Category::orderBy($sort, $order)
            ->paginate(10);

        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|unique:categories'
        ]);

        $category = Category::create($validated);
        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $rules = ['name' => 'required|max:255'];
        if ($request->slug != $category->slug) {
            $rules['slug'] = 'required|unique:categories';
        }

        $validated = $request->validate($rules);
        $category->update($validated);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->posts()->delete();
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori dan semua postingannya berhasil dihapus'
        ]);
    }
}
