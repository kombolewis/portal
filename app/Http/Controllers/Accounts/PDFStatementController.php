<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use App\Bulwark\Trans;
use App\Pension\Trans as PensionTrans;

use App\Http\Controllers\Accounts\PDFCreatorController;


class PDFStatementController extends Controller
{
    private $_member_no;
    private $_portfolio;

    public function generatePDF(Request $request){
        $request->validate([
            'member_no' => 'required|string',
            'portfolio' => 'required|string',
        ]); 

        $this->_member_no = $request->member_no;
        $this->_portfolio = $request->portfolio;
        $data = [
                    "portfolio" => $this->_portfolio,
                    "accNo" => $this->_findAcc(),
                    "member_no" => $this->_member_no
                ];
        

        $pdf = new PDFCreatorController($data);
        $pdf->SetFont('Helvetica', '',10);
        $pdf->AddPage();
        $pdf->outputControl();
        // return $pdf->Output();
        return base64_encode($pdf->Output('S'));
        
        
    }


    private function _findAcc(){
        if($this->_portfolio == 'Money Market')

            return $this->_fetchMMAcc();

        else if($this->_portfolio == 'Balanced Fund')

            return $this->_fetchBFAcc();

        else if($this->_portfolio == 'Zimele Guaranteed Pension Plan' || $this->_portfolio == 'Zimele Guaranteed Personal Pension Plan' )
        
            return $this->_fetchGPPAcc();    

        else if($this->_portfolio == 'Zimele Personal Pension Plan')

            return $this->_fetchPPAcc();

    }

    private function _fetchMMAcc(){
        return Trans::select('ACCOUNT_NO')->where('MEMBER_NO', $this->_member_no)
                                            ->where('PORTFOLIO', $this->_portfolio)
                                            ->orderBy('TRANS_DATE', 'DESC')
                                            ->pluck('ACCOUNT_NO')->first();
        
    }

    private function _fetchBFAcc(){
        return Trans::select('ACCOUNT_NO')->where('MEMBER_NO', $this->_member_no)
                                            ->where('PORTFOLIO', $this->_portfolio)
                                            ->orderBy('TRANS_DATE', 'DESC')
                                            ->pluck('ACCOUNT_NO')->first();
        
    }

    private function _fetchGPPAcc(){
        return PensionTrans::select('ACCOUNT_NO')->where('MEMBER_NO', $this->_member_no)
                                    ->where('PORTFOLIO', $this->_portfolio)
                                    ->orderBy('TRANS_DATE', 'DESC')
                                    ->pluck('ACCOUNT_NO')->first();

        

    }
    private function _fetchPPAcc(){
        return PensionTrans::select('ACCOUNT_NO')->where('MEMBER_NO', $this->_member_no)
                                                ->where('PORTFOLIO', $this->_portfolio)
                                                ->orderBy('TRANS_DATE', 'DESC')
                                                ->pluck('ACCOUNT_NO')->first();

    }
    
}
