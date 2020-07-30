<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\User as RegisteredUser;
use App\Bulwark\Member;
use App\Pension\Member as PensionMember;
use App\Http\Controllers\Auth\AuthController;

class User extends Controller
{
    public function memberState(Request $request) {

        $request->validate([
            'id_no' => 'required|string',
        ]);

        /**
         * Check if reg
         */
        
        $registeredUsers = RegisteredUser::firstWhere('id_no', $request->id_no);

        if($registeredUsers){
            /**
             * registered users
             * redirect to Login page
             * 
             */

      
            return response()->json(["status" => "11"]);
                
             
        }else{
            /**
             * unregistered/ first time users
             * check which fund they belong to
             */


            $moneyMarketMember = Member::where('PIN_NO', $request->id_no)->get();

            $pensionMember = PensionMember::where('ID_NO', $request->id_no)->get();

            if($moneyMarketMember->isEmpty() && $pensionMember->isEmpty()){
                /**
                 * Member does not exist
                 * 
                 */
                return response()->json(['status' => '00']);
                

            }else if($moneyMarketMember->isEmpty() && $pensionMember->isNotEmpty()){
                /**
                 * belong to pension
                 * find the number of accounts
                 */

                if($pensionMember->pluck('E_MAIL')->count() == 1) {
                    /**
                     * one account
                     */
                    $maker = new AuthController();
                    $maker->register($pensionMember);
                    return response()->json(["status" => "01"]);



                } else{
                    /**
                     * Multiple accounts
                     * ask the client to choose the email
                     * send only the  email and phone fields
                     * 
                     */
                
                   return $pensionMember->map(function($item){

                        $gsm = collect($item)->filter()->has('GSM_NO'); 
                        $tel_no = collect($item)->filter()->has('TEL_NO');

                        if($gsm){
                            return collect($item)->only(['E_MAIL','GSM_NO'])->all();
                        }else if($tel_no){
                            return collect($item)->only(['E_MAIL','TEL_NO'])->all();
                        }else{
                            return collect($item)->only(['E_MAIL'])->all();

                        }
                        
                    });

                }


            
            }else if($moneyMarketMember->isNotEmpty() && $pensionMember->isEmpty()){
                /**
                 * belong to money_market
                 * find the number of accounts
                 */

                if($moneyMarketMember->pluck('E_MAIL')->count() == 1) {
                    /**
                     * one account
                     */
                    $maker = new AuthController();
                    $maker->register($moneyMarketMember);
                    
                    return response()->json(["status" => "01"]);



                } else{
                    /**
                     * Multiple accounts
                     * ask the client to choose the email
                     * send only the  email and phone fields
                     * 
                     */

                
                    return $moneyMarketMember->map(function($item){

                        $gsm = collect($item)->filter()->has('GSM_NO'); 
                        $tel_no = collect($item)->filter()->has('TEL_NO');

                        if($gsm){
                            return collect($item)->only(['E_MAIL','GSM_NO'])->all();
                        }else if($tel_no){
                            return collect($item)->only(['E_MAIL','TEL_NO'])->all();
                        }else{
                            return collect($item)->only(['E_MAIL'])->all();

                        }
                        
                    });

                }


            }else if($moneyMarketMember->isNotEmpty() && $pensionMember->isNotEmpty()){
                /**
                 * belong to pension
                 * find the number of accounts
                 */

                return $moneyMarketMember->map(function($item){

                    $gsm = collect($item)->filter()->has('GSM_NO'); 
                    $tel_no = collect($item)->filter()->has('TEL_NO');

                    if($gsm){
                        return collect($item)->only(['E_MAIL','GSM_NO'])->all();
                    }else if($tel_no){
                        return collect($item)->only(['E_MAIL','TEL_NO'])->all();
                    }else{
                        return collect($item)->only(['E_MAIL'])->all();

                    }
                    
                })->concat($pensionMember->map(function($item){

                    $gsm = collect($item)->filter()->has('GSM_NO'); 
                    $tel_no = collect($item)->filter()->has('TEL_NO');

                    if($gsm){
                        return collect($item)->only(['E_MAIL','GSM_NO'])->all();
                    }else if($tel_no){
                        return collect($item)->only(['E_MAIL','TEL_NO'])->all();
                    }else{
                        return collect($item)->only(['E_MAIL'])->all();

                    }
                    
                }));

            }


        }

        
        

 

        
        
    }

    public function defineContact(Request $request){
        $request->validate([
            'id_no' => 'required|string',
            'contact' => 'required|string',
        ]);   
        
        $moneyMarketMember = Member::where('PIN_NO', $request->id_no)->where('E_MAIL', $request->contact)->get();
        $pensionMember = PensionMember::where('ID_NO', $request->id_no)->where('E_MAIL', $request->contact)->get();
        


        if($moneyMarketMember){
            $maker = new AuthController();

            $optData = Member::where('PIN_NO', $request->id_no)->get()->concat(
                PensionMember::where('ID_NO', $request->id_no)->get()
            );
            $maker->register($moneyMarketMember, $optData);
            
            return response()->json(["status" => "01"]);
        }else{
            $maker = new AuthController();
            $optData = PensionMember::where('ID_NO', $request->id_no)->get()->concat(
                Member::where('PIN_NO', $request->id_no)->get()
            );
            $maker->register($pensionMember, $optData);

            return response()->json(["status" => "01"]);   
        }
    }
}
