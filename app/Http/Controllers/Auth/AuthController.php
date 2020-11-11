<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Mail\WelcomeMail;
use App\AfricasTalking\Sms;

use App\Bulwark\Trans;
use App\Pension\Trans as PensionTrans;

class AuthController extends Controller
{
    private $_member;
    private $_optMemberNo;

    public function login($request)
    {
     
        $req = Request::create(config('services.passport.login_endpoint'), 'POST', [
            'grant_type' => 'password',
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),  
            'username' => $request->id_no,
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

    private function _filterMultiple($optData){
        
        return $optData->map(function($account){
            $optMemberNo = collect($account)->get('MEMBER_NO');
            $name = collect($account)->get('ALLNAMES');



            return collect($account)->only(['MEMBER_NO'])->merge(collect([
                'name' => $name,
                'accountPurpose' => '',
                'portfolio' => $this->_findManyPortfolio($optMemberNo,$name),

                
            ]));

        });
    }
    private function _findManyPortfolio($optMemberNo,$name){
        $transPortfolio = Trans::select('PORTFOLIO')->distinct()->where('MEMBER_NO', $optMemberNo)->pluck('PORTFOLIO')->toArray();
        $pensionPortfolio = PensionTrans::select('PORTFOLIO')->distinct()->where('MEMBER_NO', $optMemberNo)->pluck('PORTFOLIO')->toArray();

        if(count($transPortfolio) > 0 && count($pensionPortfolio) > 0){
            $transName = Trans::select('FULL_NAME')->where('MEMBER_NO', $optMemberNo)->pluck('FULL_NAME')->first();
            $pensionName = PensionTrans::select('FULL_NAME')->where('MEMBER_NO', $optMemberNo)->pluck('FULL_NAME')->first();
            
            $collectedPensionName = collect(str_split(preg_replace("/[^a-zA-Z0-9]/", "", strtolower($pensionName))));
            $collectedTransName = collect(str_split(preg_replace("/[^a-zA-Z0-9]/", "", strtolower($transName))));
            $collectedName = collect(str_split(preg_replace("/[^a-zA-Z0-9]/", "", strtolower($name))));

            $diffPension =  count($collectedPensionName->diff($collectedName));
            $diffTrans =  count($collectedTransName->diff($collectedName));
            
        
            if(($diffTrans == 0) && ($diffPension == 0)){
                return array_merge($transPortfolio,$pensionPortfolio);
            }
            else if($diffTrans == 0){
                return $transPortfolio;
            }else if($diffPension == 0){
                return $pensionPortfolio;
            }

        }
        else if(count($transPortfolio) > 0 && !(count($pensionPortfolio) > 0)){
            return $transPortfolio;
        }
        else if(!(count($transPortfolio)>0) && count($pensionPortfolio) > 0){
            return $pensionPortfolio;
        }


    }

    private function _findOnePortfolio(){

        $name = $this->_member->pluck('ALLNAMES')->first();
        $member_no = $this->_member->pluck('MEMBER_NO')->first();
        $transPortfolio = Trans::distinct()->where('MEMBER_NO', $member_no)->pluck('PORTFOLIO')->toArray();       
        $pensionPortfolio = PensionTrans::distinct()->where('MEMBER_NO', $member_no)->pluck('PORTFOLIO')->toArray();
        
        if(count($transPortfolio) > 0 && count($pensionPortfolio) > 0){
            $transName = Trans::select('FULL_NAME')->where('MEMBER_NO', $member_no)->pluck('FULL_NAME')->first();
            $pensionName = PensionTrans::select('FULL_NAME')->where('MEMBER_NO', $member_no)->pluck('FULL_NAME')->first();
            $collectedPensionName = collect(str_split(preg_replace("/[^a-zA-Z0-9]/", "", strtolower($pensionName))));
            $collectedTransName = collect(str_split(preg_replace("/[^a-zA-Z0-9]/", "", strtolower($transName))));
            $collectedName = collect(str_split(preg_replace("/[^a-zA-Z0-9]/", "", strtolower($name))));
            $diffPension =  count($collectedPensionName->diff($collectedName));
            $diffTrans =  count($collectedTransName->diff($collectedName));
            
            if(($diffTrans == 0) && ($diffPension == 0)){
                return array_merge($transPortfolio,$pensionPortfolio);
            }
            else if($diffTrans == 0){
                return $transPortfolio;
            }else if($diffPension == 0){
                return $pensionPortfolio;
            }

        }
        else if(count($transPortfolio) > 0 && !(count($pensionPortfolio) > 0)){
            return $transPortfolio;
        }
        else if(!(count($transPortfolio)>0) && count($pensionPortfolio) > 0){
            
            return $pensionPortfolio;
        }
    }

    private function _filterOne(){
        
        return $this->_member->map(function($account){
            return collect($account)->only(['MEMBER_NO'])->merge(collect([
                'name' => $this->_member->pluck('ALLNAMES')->first(),
                'portfolio' => $this->_findOnePortfolio(),
                'accountPurpose' => '',
                
            ]));

        });
    }

    public function register($member, $optData=NULL){

        $otp = bin2hex(openssl_random_pseudo_bytes(4));

        $tel = $member->pluck('GSM_NO')->first() ? $member->pluck('GSM_NO')->first() : $member->pluck('TEL_NO')->first();
        
        if($optData){
            $data = $this->_filterMultiple($optData);
        }
        else{
            $this->_member = $member;
            $data = $this->_filterOne();

        }
        $create = User::create([
            'name' => $member->pluck('ALLNAMES')->first(),
            'email' => $member->pluck('E_MAIL')->first(),
            'id_no' => $member->pluck('PIN_NO')->first(),
            'phone_no' => $tel,
            'otp' => Hash::make($otp),
            'accounts' => $data,
        ]);


        if($create)
            /***
             * send email
             */
            $email = $member->pluck('E_MAIL')->first();

            if(filter_var($email, FILTER_VALIDATE_EMAIL))
                // $response = Mail::to($email)->send(new WelcomeMail($otp));
                $response = Mail::to('kombolewis@gmail.com')->send(new WelcomeMail($otp));

            /**
             * send sms
             */
            if($tel) 
                $msg = "Hello, Welcome to Zimele Portal\n";
                $msg .= "Use this password ".$otp." to Log in to you account.\n";
                $msg .= "Thanks, \n";
                $msg .= "Zimele Customer Service.";

                $sender = new Sms();
                // return $sender->sendSms($tel,$msg);

            
            if($response)
                return $response;
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
