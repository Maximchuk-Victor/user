<?php

namespace App\Models;


class User
{
    
    public function getUser()
    {
        $user = \Auth::user();
        $user->birth_date = \Carbon\Carbon::createFromFormat('Y-m-d', $user->birth_date)->format('d.m.Y');
        return $user;
    }
}