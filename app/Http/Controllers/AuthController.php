<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // validasi
        $attrs = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        // create user
        $user = User::create([
            'name' => $attrs['name'],
            'email' => $attrs['email'],
            'password' => bcrypt($attrs['password']),
        ]);


        // return
        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken
        ], 200);
    }


    public function login(Request $request)
    {
        // validasi
        $attrs = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // attemp login
        if (!Auth::attempt($attrs)) {
            return response([
                'message' => 'invalid credentials.'
            ], 403);
        }

        // return
        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response([
            'message' => 'Logout success'
        ], 200);
    }


    public function user()
    {
        return  response([
            'user' => auth()->user()
        ], 200);
    }

    public function update(Request $request)
    {
        $attrs = $request->validate([
            'name' => 'required|string',
        ]);


        $userId = User::find(auth()->user()->id);

        $fotolama = substr($userId->image, -14);

        // hapus foto lama
        if ($userId->image <> "") {
            unlink(public_path('profiles') . '/' . $fotolama);
        }

        $image = $this->saveImage($request->image, 'profiles');

        auth()->user()->id->update([
            'name' => $attrs['name'],
            'image' => $image
        ]);

        return response([
            'message' => 'User updated.',
            'user' => auth()->user()
        ], 200);
    }
}
