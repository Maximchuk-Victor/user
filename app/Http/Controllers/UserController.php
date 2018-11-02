<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;


class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile(User $user)
    {
        $profile = $user->getUser();

        return view('profile', ['profile' => $profile]);
    }

}