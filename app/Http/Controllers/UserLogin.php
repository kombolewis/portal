<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


use App\User as RegisteredUser;
use App\Bulwark\Member;
use App\Pension\Member as PensionMember;
use App\Http\Controllers\AuthController;

class UserLogin extends Controller
{
    public function memberLogin(Request $request) {

        $request->validate([
            'id_no' => 'required|string',
            'password' => 'required|string'
        ]);

        /**
         * We have to handle first time login for users using otp
         * Handle returning normal users
         */

        $registeredUsers = RegisteredUser::firstWhere('id_no', $request->id_no);



        if(!($registeredUsers->verified)) {
            if(Hash::check($request->password, $registeredUsers->otp)){
                return response()->json(["status" => "02"]);
            }else{
                return response()->json("unknown error occurred");
    
            }
        }else{

            $maker = new AuthController();
            // error_log($request);
            $res = $maker->login($request); 
            $val = $this->_accessProtected($res, 'content');
            return $val;
            // dd($val);
            return ["true"];
        }


    }


    public function setPassword(Request $request){
        $request->validate([
            'id_no' => 'required|string',
            'password' => 'required|string'
        ]);
        
        RegisteredUser::where('id_no', $request->id_no)->update([
            'password' => Hash::make($request->password),
            'verified' => true
        ]);

        $maker = new AuthController();
        return $maker->login($request);
        // return response()->json(["status" => "22"]);

       
    }

    private function _accessProtected($obj, $prop){
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }


}
