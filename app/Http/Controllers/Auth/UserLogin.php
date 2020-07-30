<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


use App\User as RegisteredUser;
use App\Bulwark\Member;
use App\Pension\Member as PensionMember;
use App\Http\Controllers\Auth\AuthController;

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
                return response()->json("passwords did not match");
    
            }
        }else{
            /**
             * returning users
             */
            $maker = new AuthController();
            $obj = json_decode($this->_accessProtected($maker->login($request), 'content'));
            return array_merge((array)$obj, array('status' => '22'));
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
        $obj = json_decode($this->_accessProtected($maker->login($request), 'content'));
        return array_merge((array)$obj, array('status' => '22'));

       
    }

    private function _accessProtected($obj, $prop){
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }


}
