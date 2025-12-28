<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\AssignOp\Pow;

class AdminPostApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->sort ?? 'created_at';
        $order = $request->order ?? 'desc';

        $posts = Post::with('category')
                        ->orderBy($sort, $order)
                        ->paginate(10);

        return response()->json([
            'message' => 'Posts fetched successfully',
            'data' => $posts
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'required|unique:posts',
            'image' => 'nullable|image|file|max:2048',
            'category_id' => 'required|exists:categories,id',
            'body' => 'required'
        ]);

        if ($request->file('image')) {
            $validatedData['image'] = $request->file('image')->store('post-images');
        }

        $validatedData['excerpt'] = Str::limit(strip_tags($request->body), 200);

        $post = Post::create($validatedData);

        return response()->json([
            'message' => 'Post created successfully',
            'data' => $post
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load('category');
        return response()->json([
            'message' => 'Post detail fetched',
            'data' => $post
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $rules = [
            'title' => 'required|max:255',
            'image' => 'nullable|image|file|max:2048',
            'category_id' => 'required|exists:categories,id',
            'body' => 'required'
        ];

        if ($request->slug !== $post->slug) {
            $rules['slug'] = 'required|unique:posts';
        }

        $validatedData = $request->validate($rules);

        if ($request->file('image')) {
            if ($post->image) {
                Storage::delete($post->image);
            }
            $validatedData['image'] = $request->file('image')->store('post-images');
        }

        $validatedData['excerpt'] = Str::limit(strip_tags($request->body), 200);

        $post->update($validatedData);

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => $post
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->image) {
            Storage::delete($post->image);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully'
        ], 200);
    }
}
