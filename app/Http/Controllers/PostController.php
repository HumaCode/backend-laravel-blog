<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class PostController extends Controller
{
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')
                ->with('likes', function ($like) {
                    return $like->where('user_id', auth()->user()->id)
                        ->select('id', 'user_id', 'post_id')->get();
                })
                ->get()
        ]);
    }

    public function show($id)
    {
        return response([
            'post' => Post::where('id', $id)->withCount('comments', 'likes')->get()
        ]);
    }

    public function store(Request $request)
    {
        // validasi
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        // image
        $image = $this->saveImage($request->image, 'posts');

        // create
        $post = Post::create([
            'body' => $attrs['body'],
            'user_id' => auth()->user()->id,
            'image' => $image
        ]);


        return response([
            'message' => 'Post created.',
            'post' => $post,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        //ambil data post berdasarkan id
        $post = Post::find($id);

        // jika tidak ada post berdasarkan id
        if (!$post) {
            return response([
                'message' => 'Post not found.'
            ], 403);
        }

        // jika id user tidak sama dengan id session yang login 
        if ($post->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied.'
            ], 403);
        }


        // validasi
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);


        // update
        $post->update([
            'body' => $attrs['body']
        ]);



        return response([
            'message' => 'Post updated.',
            'post' => $post,
        ], 200);
    }


    public function destroy($id)
    {
        //ambil data post berdasarkan id
        $post = Post::find($id);

        $fotolama = substr($post->image, -14);

        // hapus foto lama
        if ($post->image <> "") {
            unlink(public_path('posts') . '/' . $fotolama);
        }

        // jika tidak ada post berdasarkan id
        if (!$post) {
            return response([
                'message' => 'Post not found.'
            ], 403);
        }

        // jika id user tidak sama dengan id session yang login 
        if ($post->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied.'
            ], 403);
        }



        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'message' => 'Post delete'
        ], 200);
    }
}
