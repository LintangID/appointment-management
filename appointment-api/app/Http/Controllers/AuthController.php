<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login (Request $request){
        $request->validate([
            'username' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user ) {
            throw ValidationException::withMessages([
                'username' => ['username salah.'],
            ]);
        }

        unset($user->created_at);
        unset($user->updated_at);
        unset($user->username);
        unset($user->preferred_timezone);

        $user->tokens()->delete();
        $token = $user->createToken('user login')->plainTextToken;
        $user->token = $token;

        return response()->json(['data' => $user],200);

    }

    public function logout()
    {
        $user = auth()->user();
        $user->tokens()->delete();
        return response(['message' => 'berhasil logout']);

    }

    public function me(){
        return response(['data' => auth()->user()]);
    }
}
