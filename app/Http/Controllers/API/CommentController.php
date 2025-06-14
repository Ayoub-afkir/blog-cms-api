<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Show approved comments for a specific post
    public function index(Post $post)
    {
        return $post->comments()
            ->where('approved', true)
            ->with('user:id,name')
            ->latest()
            ->get();
    }

    // Store a new comment
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->body,
            'approved' => false,
        ]);

        return response()->json([
            'message' => 'Comment submitted for review.',
            'comment' => $comment,
        ], 201);
    }

    // Approve a comment (admin only)
    public function approve(Comment $comment)
    {
        $comment->update(['approved' => true]);

        return response()->json([
            'message' => 'Comment approved.',
            'comment' => $comment,
        ]);
    }

    // Delete a comment (admin only)
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted.',
        ]);
    }
}
