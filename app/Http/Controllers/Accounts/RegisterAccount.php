<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountOpening;
use App\Mail\AccountOpeningClient;
use App\Mail\JointAccountOpening;
use App\Mail\JointAccountOpeningClient;

class RegisterAccount extends Controller
{
    public function create(Request $request){

        if($request->profile == 'individual'){

            if($request->basicFund == 'UT'){
                $data = [
                    'name' => $request->name,
                    'idno' => $request->idno,
                    'telno' => $request->telno,
                    'email' => $request->email,
                    'accTypes' => $request->accTypes,
                    'profile' => $request->profile,
                    'detailFunds' => $request->detailFunds,
                    'passportPhoto' => $request->passportPhoto,
                    'nationalId' => $request->nationalId,
                    'MM' => 'Money Market',
                    'BF' => 'Balanced Fund',
                    'ZGPP' => 'Guaranteed Personal Pension Plan',
                    'PP' => 'Pension Plan',
        
        
                ];
                return $this->_registerUTIndividual($data);

            } else if($request->basicFund == 'PE'){
                $data = [
                    'name' => $request->name,
                    'idno' => $request->idno,
                    'telno' => $request->telno,
                    'email' => $request->email,
                    'accTypes' => $request->accTypes,
                    'profile' => $request->profile,
                    'detailFunds' => $request->detailFunds,
                    'passportPhoto' => $request->passportPhoto,
                    'nationalId' => $request->nationalId,
                    'MM' => 'Money Market',
                    'BF' => 'Balanced Fund',
                    'ZGPP' => 'Guaranteed Personal Pension Plan',
                    'PP' => 'Pension Plan',
        
                ];
                return $this->_registerPEIndividual($data);
            }

            

            
               
        }else if($request->profile == 'joint'){
            $data = [
                'firstApplicantName' => $request->firstApplicantName,
                'firstApplicantPhone' => $request->firstApplicantPhone,
                'firstApplicantEmail' => $request->firstApplicantEmail,
                'secondApplicantName' => $request->secondApplicantName,
                'secondApplicantPhone' => $request->secondApplicantPhone,
                'secondApplicantEmail' => $request->secondApplicantEmail,
                'accTypes' => $request->accTypes,
                'profile' => $request->profile,
                'detailFunds' => $request->detailFunds,
                'passportPhoto' => $request->passportPhoto,
                'nationalId' => $request->nationalId,
                'MM' => 'Money Market',
                'BF' => 'Balanced Fund',
                'ZGPP' => 'Guaranteed Personal Pension Plan',
                'PP' => 'Pension Plan',
    
    
            ];
            return $this->_registerJoint($data);   
        }



    }
    private function _registerJoint($data){
        Mail::to('kombolewis@gmail.com')->send(new JointAccountOpening($data));
        // Mail::to('kombolewis@gmail.com')->send(new JointAccountOpeningClient($data));

        return response()->json(["send" => true]);
    }

    private function _registerUTIndividual($data){
        Mail::to('kombolewis@gmail.com')->send(new AccountOpening($data));
        Mail::to('kombolewis@gmail.com')->send(new AccountOpeningClient($data));
   
        return response()->json(["send" => true]);
    }

    private function _registerPEIndividual($data){

    }
    


}
