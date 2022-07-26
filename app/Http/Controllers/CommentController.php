<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    // Get all comment
    public function index($id)
    {
        $post = Post::find($id);

        if(!$post)
        {
            return response([
                'message' => 'Post not found'
            ]);
        }

        return response([
            'comments' => $post->comments()->with('user:id,name,image')->get()
        ], 200);
    }

    // Store comment
    public function store(Request $request, $id)
    {
        $post = Post::find($id);

        if(!$post)
        {
            return response([
                'message' => 'Post not found'
            ]);
        }

        // Validate fields
        $attrs = $request->validate([
            'comment' => 'required|string',
        ]);

        Comment::create([
            'comment' => $attrs['comment'],
            'post_id' => $id,
            'user_id' => auth()->user()->id
        ]);

        return response([
            'message' => 'Comment created',
        ], 200);
    }

    // Update comment
    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if(!$comment)
        {
            return response([
                'message' => 'Commend not found'
            ]);
        }

        if($comment->user_id != auth()->user()->id)
        {
            return response([
                'message' => 'Permission denied'
            ]);
        }

        // Validate fields
        $attrs = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment->update([
            'comment' => $attrs['comment']
        ]);

        return response([
            'message' => 'Comment update',
        ], 200);
    }

    // Delete comment
    public function destroy($id)
    {
        $comment = Comment::find($id);

        if(!$comment)
        {
            return response([
                'message' => 'Commend not found'
            ]);
        }

        if($comment->user_id != auth()->user()->id)
        {
            return response([
                'message' => 'Permission denied'
            ]);
        }

        $comment->delete();

        return response([
            'message' => 'Comment deleted'
        ]);
    }
}
