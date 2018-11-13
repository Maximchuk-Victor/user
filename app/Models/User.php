<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class User
{
    
    public function getUser()
    {
        return \Auth::user();
    }
    
    public function createUser($data, $avatare)
    {
        //dd($avatare->name());
        $user = \Auth::user();
        if($avatare !== null){
            $st = Storage::disk('public')->put('/avatars', $avatare);
            $url = Storage::disk('public')->url($st);
            Storage::disk('public')->setVisibility('/avatars', 'public');
        }
        
        $user->birth_date = $data['birth_date'];
        $user->avatare = $url;
        $user->save();
        
        return $this->getUser();
    }
}