<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    public function redirect(){
        return Socialite::driver('facebook')->redirect();
    }

    public function callbackFacebook(){
        try
        {

            $google_user = Socialite::driver('facebook')->stateless()->user();
            $user = User::where('google_id', $google_user->getId())->first();
            if (!$user)
            {
                $userData = [
                    'name' =>       $google_user->getName(),
                    'email' =>      $google_user->getEmail(),
                    'google_id' =>  $google_user->getId(),
                    'profile_photo_path' => $google_user->getAvatar()
                ];

                $user = User::create($userData);
            }
            

            $autUser = User::find($user->id);
            Auth::login($autUser);
            return redirect()->route('dashboard');
        }
        catch (\Exception $e)
        {
            Log::error($e);
            return redirect()->route('login');
        }
    }
}
