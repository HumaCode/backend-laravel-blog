<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($id)
    {
        //ambil data post berdasarkan id
        $post = Post::find($id);

        // jika tidak ada post berdasarkan id
        if (!$post) {
            return response([
                'message' => 'Post not found.'
            ], 403);
        }

        return response([
            'comments' => $post->comments()->with('user:id,name,image')->get(),
        ], 200);
    }


    public function store(Request $request, $id)
    {
        //ambil data post berdasarkan id
        $post = Post::find($id);

        // jika tidak ada post berdasarkan id
        if (!$post) {
            return response([
                'message' => 'Post not found.'
            ], 403);
        }


        // validasi
        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        // create
        Comment::create([
            'comment' => $attrs['comment'],
            'post_id' => $id,
            'user_id' => auth()->user()->id,
        ]);

        return response([
            'message' => 'Comment created.',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        // jika tidak ada comment berdasarkan id
        if (!$comment) {
            return response([
                'message' => 'Comment not found.'
            ], 403);
        }

        // jika id user tidak sama dengan id session yang login 
        if ($comment->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied.'
            ], 403);
        }



        // validasi
        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        // update
        $comment::update([
            'comment' => $attrs['comment']
        ]);

        return response([
            'message' => 'Comment updated.'
        ], 200);
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);

        // jika tidak ada comment berdasarkan id
        if (!$comment) {
            return response([
                'message' => 'Comment not found.'
            ], 403);
        }

        // jika id user tidak sama dengan id session yang login 
        if ($comment->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied.'
            ], 403);
        }

        // delete 
        $comment->delete();

        return response([
            'message' => 'Comment deleted.'
        ], 200);
    }
}
