<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return Post::with('user')->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required',
            'status' => 'in:draft,published'
        ]);

        $post = Post::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'body' => $request->body,
            'status' => $request->status ?? 'draft',
        ]);

        return response()->json($post, 201);
    }

    public function show($id)
    {
        return Post::with('user')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($request->user()->id !== $post->user_id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->update($request->only(['title', 'body', 'status']));

        return response()->json($post);
    }

    public function destroy(Request $request,$id)
    {
        $post = Post::findOrFail($id);

        if ($request->user()->id !== $post->user_id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted']);
    }
}
