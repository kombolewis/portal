<?php

namespace App\Http\Controllers\Accounts;

use App\Bulwark\Member;
use App\Pension\Member as PensionMember;
use App\Bulwark\Trans;
use App\Pension\Trans as PensionTrans;
use App\Bulwark\Nav as TransNav;
use App\Pension\Nav as PensionNav;

use Codedge\Fpdf\Fpdf\Fpdf;



class PDFCreatorController extends FPDF
{
    private $_data;
    private $_memberData;
    private $_arrData;
    private $_header;
    private $_headerSummary;
    private $_url;
    private $_nav;

    function __construct($data){
        $this->_data = collect($data);
        $this->_url = 'https://chama.zimele.co.ke/chamamanager/Zimele-Statements/Zimele_logo.jpg';
        if($this->_data->get('portfolio') == 'Balanced Fund' || $this->_data->get('portfolio') == 'Zimele Personal Pension Plan'){
            parent::__construct('L','mm','A4');

        }else{
            parent::__construct('P','mm','A4');

        }
        $this->_headerSummary = [
            'Summary','Totals'
        ];

        $this->_utils();

    }

    private function _utils(){
        if($this->_data->get('portfolio') == 'Balanced Fund' || $this->_data->get('portfolio') == 'Zimele Personal Pension Plan'){
            if($this->_data->get('portfolio') == 'Balanced Fund'){
                $this->_arrData = Trans::where('ACCOUNT_NO',  $this->_data->get('accNo'))
                                                            ->orderBy('TRANS_DATE', 'ASC')
                                                            ->get();
                $this->_memberData = Member::where('MEMBER_NO', $this->_data->get('member_no'));
                $this->_nav = TransNav::select('AMOUNT')->orderBy('NAV_DATE', 'DESC')->pluck('AMOUNT')->first();


            }else{
                $this->_arrData = PensionTrans::where('ACCOUNT_NO',  $this->_data->get('accNo'))
                                                                    ->orderBy('TRANS_DATE', 'ASC')
                                                                    ->get();
                $this->_memberData = PensionMember::where('MEMBER_NO', $this->_data->get('member_no'))->get();
                $this->_nav = PensionNav::select('AMOUNT')->orderBy('NAV_DATE', 'DESC')->pluck('AMOUNT')->first();

            }
            $this->_header = [
                'Date','Description','Amt. Deposit','Units Bought','Buying Price',
                'Amt. Withdraw','Units Sold',
                'Selling Price','Gain/(Loss)', '%Gain/(Loss)'
            ];

        }else{

            if($this->_data->get('portfolio') == 'Money Market'){
                $this->_arrData = Trans::where('ACCOUNT_NO',  $this->_data->get('accNo'))
                                                            ->orderBy('TRANS_DATE', 'ASC')
                                                            ->get();
                $this->_memberData = Member::where('MEMBER_NO', $this->_data->get('member_no'))->get();
            }else{
                $this->_arrData = PensionTrans::where('ACCOUNT_NO',  $this->_data->get('accNo'))
                                                                    ->orderBy('TRANS_DATE', 'ASC')
                                                                    ->get();
                $this->_memberData = PensionMember::where('MEMBER_NO', $this->_data->get('member_no'))->get();
            }
            $this->_header = [
                'Date','Description','Deposit', 'Interest','Withdrawal',
            ];

        }
    }
    function outputControl(){

        if($this->_data->get('portfolio') == 'Money Market'){
            $this->fetchMMAcc();
            $this->fetchMMAccSummary();          
        }else if($this->_data->get('portfolio') == 'Balanced Fund'){
            $this->fetchBFAcc();
            $this->fetchBFAccSummary();
        }else if($this->_data->get('portfolio') == 'Zimele Guaranteed Pension Plan' || $this->_data->get('portfolio') == 'Zimele Guaranteed Personal Pension Plan' ){
            $this->fetchMMAcc();
            $this->fetchMMAccSummary();  
        }else if($this->_data->get('portfolio') == 'Zimele Personal Pension Plan'){
            $this->fetchBFAcc();
            $this->fetchBFAccSummary();
        }
    }
    
    
    function Header() {
        $this->SetFont('Helvetica','B',13);
        $this->SetTextColor(0,0,0);
        if($this->_data->get('portfolio') == 'Balanced Fund' || $this->_data->get('portfolio') == 'Zimele Personal Pension Plan')
            $this->image($this->_url, 131.9, 2, 32, 6.5);
        else
            $this->image($this->_url, 88.9, 2, 32, 6.5);
        $this->ln(1.3);
        $this->Cell(0,0,'Zimele Unit Trust',0,1,'C');
        $this->SetFont('Helvetica','B',10);
        $this->ln(-5.0);
        $this->Cell(0,21,'Telephone: 254-722-207662',0,0,'L');
        if($this->_data->get('portfolio') == 'Balanced Fund' || $this->_data->get('portfolio') == 'Zimele Personal Pension Plan')
            $this->Cell(-274.5,21,'E-Mail: admin@zimele.net',0,0,'C');
        else
            $this->Cell(-190.5,21,'E-Mail: admin@zimele.net',0,0,'C');
        $this->Cell(0,21,'Website: www.zimele.co.ke',0,1,'R');
        $this->ln(-17.2);
        $this->Cell(0,22,'_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _  ',0,1,'C');
        $this->ln(-9.5);
        
    }   

    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Helvetica','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'R');
    }


    function fetchMMAcc() {
        $this->SetFont('Helvetica','B',10);
        $this->SetTextColor(0,0,0);
        $this->Cell(0,10,'ACCOUNT STATEMENT',0,1,'C');
        $this->ln(-10.5);
        $this->Cell(0,23,date("D, d-F-Y h:i:s A"),0,1,'C');
        $this->ln(-32);



        
        $contact = $this->_memberData->pluck('E_MAIL')->first();
        $name = $this->_memberData->pluck('ALLNAMES')->first();
        
        $this->SetFont('Helvetica','B',10);
        $this->SetTextColor(0,0,0);
        $this->Cell(0,42,'Member Details:',0,1,'L');
        $this->ln(-37.5);
        $this->Cell(0,43,trim($contact, "-"),0,1,'L'); 
        $this->ln(-38.5);
        $this->Cell(0,44,ucwords(trim($name,".--")),0,1,'L'); 
        $this->ln(-42);
        $this->SetFont('Helvetica','',10);


        $this->SetFont('Helvetica','B',10);
        $this->SetTextColor(0,0,0);
        $this->Cell(0,16,'Account No.:',0,1,'R');
        $this->ln(-22.5);
        $this->Cell(0,39,$this->_data->get('accNo'),0,1,'R');
        $this->ln(-5);
     

        // Colors, line width and bold font
        $this->SetFillColor(36,33,36);
        $this->SetTextColor(255);
        $this->SetDrawColor(36,33,36);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Header

        $w = array(30.99999, 65.99999, 30.99999, 30.99999, 30.99999);
        for($i=0;$i<count($this->_header);$i++)
            $this->Cell($w[$i],6,$this->_header[$i],1,0,'C',true);
        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224,224,224);
        $this->SetTextColor(0);
        $this->SetFont('');
        // DataarrData
    
        $fill = false; 
        
        foreach($this->_arrData as $row2){
            if(
                $row2['TRANS_TYPE'] == 'PURCHASE' ||
                $row2['TRANS_TYPE'] == 'DEPOSIT' ||
                $row2['TRANS_TYPE'] == 'Deposit'
                
            ){
            
                $this->Cell($w[0],6,trim($row2['TRANS_DATE'], "00:00:00"),1,0,'L',$fill);
                $space = " ";
                $details = (strtoupper($row2['MOP']).$space.trim($row2['DRAWERPAYEE'],"0"));
                $this->Cell($w[1],6,trim($details, "-."),1,0,'L',$fill);
                $this->Cell($w[2],6,number_format($row2['AMOUNT'], 2, '.', ','),1,0,'R',$fill);
                $this->Cell($w[3],6,'',1,0,'L',$fill);
                $this->Cell($w[4],6,'',1,0,'L',$fill);
                $this->Ln();
                $fill = !$fill;  
            }
            if($row2['TRANS_TYPE'] == 'WITHDRAWAL'){
                $this->Cell($w[0],6,trim($row2['TRANS_DATE'], "00:00:00"),1,0,'L',$fill);
                $space = " ";
                $details2 = (strtoupper($row2['MOP']).$space.trim($row2['DRAWERPAYEE'],"0"));
                $this->Cell($w[1],6,trim($details2, "-.0"),1,0,'L',$fill);
                $this->Cell($w[2],6,'',1,0,'L',$fill);
                $this->Cell($w[3],6,number_format($row2['AMOUNT'], 2, '.', ','),1,0,'R',$fill);
                $this->Cell($w[4],6,'',1,0,'L',$fill);
                $this->Ln();
                $fill = !$fill;  
            }
            if(
                $row2['TRANS_TYPE'] == 'INTEREST' ||
                $row2['TRANS_TYPE'] == 'INTERST'
                ){
                $this->Cell($w[0],6,trim($row2['TRANS_DATE'], "00:00:00"),1,0,'L',$fill);
                $space = " ";
                $details3= (strtoupper($row2['MOP']).$space.trim($row2['DRAWERPAYEE'],"0"));
                $this->Cell($w[1],6,trim($details3, "-."),1,0,'L',$fill);
                $this->Cell($w[2],6,'',1,0,'L',$fill);
                $this->Cell($w[3],6,'',1,0,'L',$fill);
                $this->Cell($w[4],6,number_format($row2['AMOUNT'], 2, '.', ','),1,0,'R',$fill);
                $this->Ln();
                $fill = !$fill;  
                
            }
                    
        }                    
    }

    function fetchBFAcc() {
        $this->SetFont('Helvetica','B',10);
        $this->SetTextColor(0,0,0);
        $this->Cell(0,10,'ACCOUNT STATEMENT',0,1,'C');
        $this->ln(-10.5);
        $this->Cell(0,23,date("D, d-F-Y h:i:s A"),0,1,'C');
        $this->ln(-32);



        
        $contact = $this->_memberData->pluck('E_MAIL')->first();
        $name = $this->_memberData->pluck('ALLNAMES')->first();
        
        $this->SetFont('Helvetica','B',10);
        $this->SetTextColor(0,0,0);
        $this->Cell(0,42,'Member Details:',0,1,'L');
        $this->ln(-37.5);
        $this->Cell(0,43,trim($contact, "-"),0,1,'L'); 
        $this->ln(-38.5);
        $this->Cell(0,44,ucwords(trim($name,".--")),0,1,'L'); 
        $this->ln(-42);
        $this->SetFont('Helvetica','',10);


        $this->SetFont('Helvetica','B',10);
        $this->SetTextColor(0,0,0);
        $this->Cell(0,16,'Account No.:',0,1,'R');
        $this->ln(-22.5);
        $this->Cell(0,39,$this->_data->get('accNo'),0,1,'R');
        $this->ln(-5);
     

        // Colors, line width and bold font
        $this->SetFillColor(36,33,36);
        $this->SetTextColor(255);
        $this->SetDrawColor(36,33,36);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Header

        $w = array(25,72,24,26,24,25,20,24,22,22);
        for($i=0;$i<count($this->_header);$i++)
            $this->Cell($w[$i],6,$this->_header[$i],1,0,'C',true);
        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224,224,224);
        $this->SetTextColor(0);
        $this->SetFont('');
        // DataarrData
    
        $fill = false; 
        
        foreach($this->_arrData as $row2){

            if(
                $row2['TRANS_TYPE'] == 'PURCHASE' ||
                $row2['TRANS_TYPE'] == 'Purchase' ||
                $row2['TRANS_TYPE'] == 'DEPOSIT' ||
                $row2['TRANS_TYPE'] == 'Deposit'
                
            ){
                $gl = (($row2['NOOFSHARES']*$row2['NAV']) - ($row2['AMOUNT']));
                $glp = ($gl/($row2['AMOUNT'])) * 100;

                $this->Cell($w[0],6,trim($row2['TRANS_DATE'], "00:00:00"),1,0,'L',$fill);
                $space = " ";
                $details = (strtoupper($row2['MOP']).$space.trim($row2['DRAWERPAYEE'],"0"));
                $this->Cell($w[1],6,trim($details, "-."),1,0,'L',$fill);
                $this->Cell($w[2],6,number_format($row2['AMOUNT'], 2, '.', ','),1,0,'R',$fill);
                $this->Cell($w[3],6,number_format($row2['NOOFSHARES'],2, '.', ','),1,0,'L',$fill);
                $this->Cell($w[4],6,number_format($row2['PRICE'],4),1,0,'L',$fill);
                $this->Cell($w[5],6,'',1,0,'L',$fill); //aW
                $this->Cell($w[6],6,'',1,0,'L',$fill);
                $this->Cell($w[7],6,number_format($row2['NAV'],4),1,0,'L',$fill);//SP
                $this->Cell($w[8],6,number_format($gl,2,'.', ','),1,0,'L',$fill);
                $this->Cell($w[9],6,number_format($glp,2),1,0,'L',$fill);
                $this->Ln();
                $fill = !$fill;  
            }
            if($row2['TRANS_TYPE'] == 'WITHDRAWAL'){
                $ratio = ($row2['AMOUNT']/($row2['NOOFSHARES']*$row2['NAV']));

                $this->Cell($w[0],6,trim($row2['TRANS_DATE'], "00:00:00"),1,0,'L',$fill);
                $space = " ";
                $details = (strtoupper($row2['MOP']).$space.trim($row2['DRAWERPAYEE'],"0"));
                $this->Cell($w[1],6,trim($details, "-."),1,0,'L',$fill);
                $this->Cell($w[2],6,'',1,0,'R',$fill);
                $this->Cell($w[3],6,'',1,0,'L',$fill);
                $this->Cell($w[4],6,number_format($row2['PRICE'],4),1,0,'L',$fill);
                $this->Cell($w[5],6,number_format($row2['AMOUNT'], 2, '.', ','),1,0,'L',$fill); //aW
                $this->Cell($w[6],6,number_format($row2['NOOFSHARES'], 2, '.', ','),1,0,'L',$fill);
                $this->Cell($w[7],6,number_format($row2['NAV'],4),1,0,'L',$fill);//SP
                $this->Cell($w[8],6,'',1,0,'L',$fill);
                $this->Cell($w[9],6,'',1,0,'L',$fill);
                $this->Ln();
                $fill = !$fill;  
            }
                    
        }                    
    }

    function fetchMMAccSummary() {
        
        $this->Ln(10);
        // Colors, line width and bold font
        $this->SetFillColor(36,33,36);
        $this->SetTextColor(255);
        $this->SetDrawColor(36,33,36);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Header
    
        $v = array(95.99999, 30.99999);
        for($i=0;$i<count($this->_headerSummary);$i++)
            $this->Cell($v[$i],6,$this->_headerSummary[$i],1,0,'C',true);
        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224,224,224);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;

        $totaldeps = 0;
        $totalwiths  = 0;
        $totalint = 0;

        foreach($this->_arrData as $row2){
            if(
                $row2['TRANS_TYPE'] == 'PURCHASE' ||
                $row2['TRANS_TYPE'] == 'DEPOSIT' ||
                $row2['TRANS_TYPE'] == 'Deposit'
                
            ){
                $totaldeps += ($row2['AMOUNT']); 
            
            }
            if(
                $row2['TRANS_TYPE'] == 'Re investing' ||
                $row2['TRANS_TYPE'] == 'Re-investing' ||
                $row2['TRANS_TYPE'] == 'Re-Investing'||
                $row2['TRANS_TYPE'] == 'Re-investment'||
                $row2['TRANS_TYPE'] == 'Re-INVESTMENT'||
                $row2['TRANS_TYPE'] == 'Re-INVESTING'
                
            ){
                $totaldeps += ($row2['AMOUNT']); 
            
            }
            if($row2['TRANS_TYPE'] == 'WITHDRAWAL'){
                $totalwiths += ($row2['AMOUNT']); 

            }
            if(
                $row2['TRANS_TYPE'] == 'INTEREST' ||
                $row2['TRANS_TYPE'] == 'INTERST'
                ){

                $totalint += ($row2['AMOUNT']);
                
            }
                    
        }   

        
        $TOT = ($totalint+$totaldeps)-$totalwiths;
            
        $this->Cell($v[0],6,'Total Deposits',1,0,'L',$fill);
        $this->Cell($v[1],6,trim(number_format($totaldeps, 2, '.', ','),"-"),1,1,'R',$fill);
        $this->SetFillColor(224,224,224);
    
        $this->Cell($v[0],6,'Total Withdrawals',1,0,'L',!$fill);
        $this->Cell($v[1],6,trim(number_format($totalwiths, 2, '.', ','),"-"),1,1,'R',!$fill);
            
        $this->Cell($v[0],6,'Total Interest',1,0,'L',$fill);
        $this->Cell($v[1],6,trim(number_format($totalint, 2, '.', ','),"-"),1,1,'R',$fill);
            
        $this->SetFont('Helvetica','B',12);
        $this->Cell($v[0],9,'Account Balance',1,0,'L',!$fill);
        $this->Cell($v[1],9,trim(number_format($TOT, 2, '.', ','),"-"),1,1,'R',!$fill);
    }

    function fetchBFAccSummary() {
        
        $this->Ln(10);
        // Colors, line width and bold font
        $this->SetFillColor(36,33,36);
        $this->SetTextColor(255);
        $this->SetDrawColor(36,33,36);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Header
    
        $v = array(95.99999, 30.99999);
        for($i=0;$i<count($this->_headerSummary);$i++)
            $this->Cell($v[$i],6,$this->_headerSummary[$i],1,0,'C',true);
        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224,224,224);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;

        $totaldeps = 0;
        $totalUnitsBought = 0;
        $totalUnitsWithdrawn = 0;
        $totalwiths  = 0;

        foreach($this->_arrData as $row2){
            if(
                $row2['TRANS_TYPE'] == 'PURCHASE' ||
                $row2['TRANS_TYPE'] == 'Purchase' ||
                $row2['TRANS_TYPE'] == 'DEPOSIT' ||
                $row2['TRANS_TYPE'] == 'Deposit'
                
            ){
                $totaldeps += ($row2['AMOUNT']);
                $totalUnitsBought += $row2['NOOFSHARES'];
            
            }
            if($row2['TRANS_TYPE'] == 'WITHDRAWAL'){
                $totalwiths += ($row2['AMOUNT']); 
                $totalUnitsWithdrawn += $row2['NOOFSHARES'];

            }
                    
        }   

        
        $unitsBalance = $totalUnitsBought-$totalUnitsWithdrawn;
        $currentPrice = $this->_nav;
        $marketValue = $unitsBalance * $currentPrice;

            
        $this->Cell($v[0],6,'Total Deposits',1,0,'L',$fill);
        $this->Cell($v[1],6,trim(number_format($totaldeps, 2, '.', ','),"-"),1,1,'R',$fill);
        $this->SetFillColor(224,224,224);
    
        $this->Cell($v[0],6,'Total Units Bought',1,0,'L',!$fill);
        $this->Cell($v[1],6,trim(number_format($totalUnitsBought, 2, '.', ','),"-"),1,1,'R',!$fill);

        $this->Cell($v[0],6,'Total Withdrawals',1,0,'L',$fill);
        $this->Cell($v[1],6,trim(number_format($totalwiths, 2, '.', ','),"-"),1,1,'R',$fill);
        
        $this->Cell($v[0],6,'Total Units Sold',1,0,'L',!$fill);
        $this->Cell($v[1],6,trim(number_format($totalUnitsWithdrawn, 2, '.', ','),"-"),1,1,'R',!$fill);
            
            
        $this->SetFont('Helvetica','B',12);
        $this->Cell($v[0],9,'Unit Balance',1,0,'L',$fill);
        $this->Cell($v[1],9,trim(number_format($unitsBalance, 2, '.', ','),"-"),1,1,'R',$fill);

        $this->Cell($v[0],9,'Current Price',1,0,'L',!$fill);
        $this->Cell($v[1],9,trim(number_format($currentPrice, 2, '.', ','),"-"),1,1,'R',!$fill);

        $this->Cell($v[0],9,'Market Value',1,0,'L',$fill);
        $this->Cell($v[1],9,trim(number_format($marketValue, 2, '.', ','),"-"),1,1,'R',$fill);
    }

}