<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function likeOrUnlike($id)
    {
        //ambil data post berdasarkan id
        $post = Post::find($id);

        // jika tidak ada post berdasarkan id
        if (!$post) {
            return response([
                'message' => 'Post not found.'
            ], 403);
        }

        $like = $post->likes()->where('user_id', auth()->user()->id)->first();

        // jika belum di like/akan di  like
        if (!$like) {
            Like::create([
                'post_id' => $id,
                'user_id' => auth()->user()->id,
            ]);

            return response([
                'message' => 'Liked'
            ]);
        }

        // dislike
        $like->delete();

        return response([
            'messages' => 'Disliked'
        ]);
    }
}
