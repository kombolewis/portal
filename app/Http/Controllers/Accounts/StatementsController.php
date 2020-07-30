<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Bulwark\Trans;
use App\Pension\Trans as PensionTrans;

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
        $this->portfolio = $request->portfolio;
        
        if($request->portfolio == 'Money Market')

            return $this->_fetchMMTrans();

        else if($request->portfolio == 'Balanced Fund')

            return $this->_fetchBFTrans();

        else if($request->portfolio == 'Zimele Guaranteed Pension Plan' || $request->portfolio == 'Zimele Guaranteed Personal Pension Plan' )
           
            return $this->_fetchGPPTrans();    

        else if($request->portfolio == 'Zimele Personal Pension Plan')

            return $this->_fetchPPTrans();
    }


    private function _fetchMMTrans(){
        $acc_no =  Trans::select('ACCOUNT_NO')->where('MEMBER_NO', $this->_member_no)
                                            ->orWhere('PORTFOLIO', $this->_portfolio)
                                            ->orderBy('TRANS_DATE', 'DESC')
                                            ->pluck('ACCOUNT_NO')->first();

        return Trans::where('ACCOUNT_NO', $acc_no)->get();
    }

    private function _fetchBFTrans(){
        $acc_no =  Trans::select('ACCOUNT_NO')->where('MEMBER_NO', $this->_member_no)
                                            ->orWhere('PORTFOLIO', $this->_portfolio)
                                            ->orderBy('TRANS_DATE', 'DESC')
                                            ->pluck('ACCOUNT_NO')->first();

        return Trans::where('ACCOUNT_NO', $acc_no)->get();

        
    }

    private function _fetchGPPTrans(){
        $acc_no =  PensionTrans::select('ACCOUNT_NO')->where('MEMBER_NO', $this->_member_no)
                                    ->orWhere('PORTFOLIO', $this->_portfolio)
                                    ->orderBy('TRANS_DATE', 'DESC')
                                    ->pluck('ACCOUNT_NO')->first();

        return PensionTrans::where('ACCOUNT_NO', $acc_no)->get();

    }
    private function _fetchPPTrans(){
        $acc_no =  PensionTrans::select('ACCOUNT_NO')->where('MEMBER_NO', $this->_member_no)
                                                ->orWhere('PORTFOLIO', $this->_portfolio)
                                                ->orderBy('TRANS_DATE', 'DESC')
                                                ->pluck('ACCOUNT_NO')->first();

        return PensionTrans::where('ACCOUNT_NO', $acc_no)->get();
    }
 
}
