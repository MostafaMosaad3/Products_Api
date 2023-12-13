<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use  Illuminate\Support\Facades\Hash ;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $credintionals = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $credintionals['name'],
            'email' => $credintionals['email'],
            'password' => bcrypt($credintionals['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $responce = [
            'user' => $user,
            'token' => $token
        ];

        return Response($responce, 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password ))
        {
        return response([
            'message' => 'email or password is unvalid'],
            '401'
        );
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $responce = [
            'user' => $user,
            'token' => $token
        ];

        return Response($responce, 201);
        }


    public function logout(Request $request){
        auth()->user()->tokens()->delete() ;

        return [
            'message'=>'logged out'
        ];
    }
}
