<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index ()
    {
        $users = User::whereNot('id', Auth::id())->get();
        return response()->json($users,200);
    }
}
