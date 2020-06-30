<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Mail\WelcomeMail;
class AuthController extends Controller
{
    public function login(Request $request)
    {

        $req = Request::create(config('services.passport.login_endpoint'), 'POST', [
            'grant_type' => 'password',
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),  
            'username' => $request->username,
            'password' => $request->password,

        ]);


        $response = app()->handle($req);
        if ($response->status() == 400) {
            return response()->json([
                'message' => 'Invalid request. Please enter a username or password'
            ]);

        } else if ($response->status() == 401) {
            return response()->json([
                'message' => 'Your credentials are incorrect. Please try again'
            ]);
        }

        return $response;
    }

    public static function register($member){

        $otp = bin2hex(openssl_random_pseudo_bytes(4));

    //    $obj = collect($member);
        $create = User::create([
            'name' => $member->pluck('ALLNAMES')->first() ? $member->pluck('ALLNAMES')->first() : $member->ALLNAMES,
            'email' => $member->pluck('E_MAIL')->first() ? $member->pluck('E_MAIL')->first() : $member->E_MAIL,
            'id_no' => $member->pluck('PIN_NO')->first() ?$member->pluck('PIN_NO')->first() : $member->PIN_NO,
            'otp' => Hash::make($otp),

        ]);


        if($create)
            Mail::to($member->pluck('E_MAIL')->first())->send(new WelcomeMail($otp));
            return;
    }

    public function logout(){
        /**
         * revoke and Tokens
         * Tokens directly from hasapiTokens returns all the tokens
         * revoke comes from the model
         * user information is present in the authentication middleware.
         */

        auth()->user()->tokens->each(function ($token, $key){
            $token->delete();
        });

        return response()->json('Logged out successfully', 200);


    }
}
