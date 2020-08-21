<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Bulwark\Trans;
use App\Bulwark\Nav as TransNav;
use App\Pension\Trans as PensionTrans;
use App\Pension\Nav as PensionNav;

class StatementsController extends Controller
{
    private $_member_no;
    private $_portfolio;

    public function index(Request $request){

        $request->validate([
            'member_no' => 'required|string',
            'portfolio' => 'required|string',
        ]); 
        
        $this->_member_no = $request->member_no;
        $this->_portfolio = $request->portfolio;
        
        if($request->portfolio == 'Money Market')

            return $this->_fetchMMTrans();

        else if($request->portfolio == 'Balanced Fund')

            // return $this->calculateNav();
            return $this->_fetchBFTrans();


        else if($request->portfolio == 'Zimele Guaranteed Pension Plan' || $request->portfolio == 'Zimele Guaranteed Personal Pension Plan' )
           
            return $this->_fetchGPPTrans();    

        else if($request->portfolio == 'Zimele Personal Pension Plan')

            return $this->_fetchPPTrans();
    }


    private function _fetchMMTrans(){
        $acc_no =  Trans::select('ACCOUNT_NO')->where('MEMBER_NO', $this->_member_no)
                                            ->where('PORTFOLIO', $this->_portfolio)
                                            ->orderBy('TRANS_DATE', 'DESC')
                                            ->pluck('ACCOUNT_NO')->first();
        // return $acc_no;
        return Trans::where('ACCOUNT_NO', $acc_no)->get();
    }

    private function _fetchBFTrans(){
        $acc_no =  Trans::select('ACCOUNT_NO')->where('MEMBER_NO', $this->_member_no)
                                            ->where('PORTFOLIO', $this->_portfolio)
                                            ->orderBy('TRANS_DATE', 'DESC')
                                            ->pluck('ACCOUNT_NO')->first();

        return Trans::where('ACCOUNT_NO', $acc_no)->get();

        
    }

    private function _fetchGPPTrans(){
        $acc_no =  PensionTrans::select('ACCOUNT_NO')->where('MEMBER_NO', $this->_member_no)
                                    ->where('PORTFOLIO', $this->_portfolio)
                                    ->orderBy('TRANS_DATE', 'DESC')
                                    ->pluck('ACCOUNT_NO')->first();

        return PensionTrans::where('ACCOUNT_NO', $acc_no)->get();

    }
    private function _fetchPPTrans(){
        $acc_no =  PensionTrans::select('ACCOUNT_NO')->where('MEMBER_NO', $this->_member_no)
                                                ->where('PORTFOLIO', $this->_portfolio)
                                                ->orderBy('TRANS_DATE', 'DESC')
                                                ->pluck('ACCOUNT_NO')->first();

        return PensionTrans::where('ACCOUNT_NO', $acc_no)->get();
    }
    
    public function calculateNav(Request $request) {

        $request->validate([
            'portfolio' => 'required|string',
        ]); 

        if($request->portfolio == 'Balanced Fund')
            return TransNav::select('AMOUNT')->orderBy('NAV_DATE', 'DESC')->pluck('AMOUNT')->first();

        elseif($request->portfolio == 'Zimele Personal Pension Plan')
            return PensionNav::select('AMOUNT')->orderBy('NAV_DATE', 'DESC')->pluck('AMOUNT')->first();
            

        
    }  

}
