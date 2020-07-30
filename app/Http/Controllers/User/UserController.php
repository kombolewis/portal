<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Bulwark\Member;
use App\Bulwark\Trans;
use App\Pension\Member as PensionMember;
use App\Pension\Trans as PensionTrans;
use App\Http\Resources\Money_Market as MoneyMarketResource;
use App\Http\Resources\Pension as PensionResource;
use App\User;

class UserController extends Controller
{   
    private $_singleAccount = [];
    private $_multipleAccount = [];

    public function index(){
        
        /**
         * we need to set the purpose and differentiate bf and moneymarket account, pension,GPP
         */
        $accounts = auth()->user()->accounts;
        if(count($accounts) == 1){
            /**
             * set the account as a class property
             */
            $this->_singleAccount = collect(collect($accounts)->first());

            // return $this->_singleAccount->get('portfolio');

            
            
            /**
             * check if the user has multiple portfolios 
             */
            if(count($this->_singleAccount->get('portfolio')) > 1){
                /**
                 * single account with multiple portfolios either pension or MM
                 * BF and MMF, GPP, PP atleast 2 of these
                 */
                return $this->_resolveForSingleDoublePortAcc();

            }else{
                /**
                 * single portfolio
                 * either BF,MMF,GPP,PP
                 */

                return $this->_resolveForSingleSolePortAcc();
                

            }


        }else if(count($accounts) > 1){

            $this->_multipleAccount  = $accounts;

            return $this->_resolveForMultipleAcc();


        }




    }


    private function _resolveForSingleSolePortAcc(){

        $member_no = $this->_singleAccount->get('MEMBER_NO');
                
        $accountPurpose = $this->_singleAccount->get('accountPurpose');

        $sessionPortfolio = $this->_singleAccount->get('portfolio');

        $transPortfolio = Trans::select('PORTFOLIO')->distinct()->where('MEMBER_NO', $member_no)->get()->pluck('PORTFOLIO')->first();
        
        $pensionPortfolio = pensionTrans::select('PORTFOLIO')->distinct()->where('MEMBER_NO', $member_no)->get()->pluck('PORTFOLIO')->first();
        

        
       
        if($transPortfolio == $sessionPortfolio[0]){
            $data = MoneyMarketResource::collection(Member::where('PIN_NO', auth()->user()->id_no)->get());    
            $result = collect($data->first())->put('PORTFOLIO', $sessionPortfolio[0])
                                                ->put('accountPurpose', $accountPurpose);
            return array (
            'money_market' => [$result],
            'pension' => []
           );
        }else if($pensionPortfolio == $sessionPortfolio[0]){
            $data = PensionResource::collection(PensionMember::where('ID_NO', auth()->user()->id_no)->get());    
            $result = collect($data->first())->put('PORTFOLIO', $sessionPortfolio[0]);
            return array (
            'money_market' => [],
            'pension' => [$result]
           );
        }

    }

    private function _resolveForSingleDoublePortAcc(){

                
        $member_no = $this->_singleAccount->get('MEMBER_NO');

        $name = $this->_singleAccount->get('name');

        $accountPurpose = $this->_singleAccount->get('accountPurpose');

        $sessionPortfolio = $this->_singleAccount->get('portfolio');

        $transPortfolio = Trans::select('PORTFOLIO')->distinct()->where('MEMBER_NO', $member_no)->get()->pluck('PORTFOLIO');
        
        $pensionPortfolio = pensionTrans::select('PORTFOLIO')->distinct()->where('MEMBER_NO', $member_no)->get()->pluck('PORTFOLIO');
       
        $transName = Trans::select('FULL_NAME')->where('MEMBER_NO', $member_no)->pluck('FULL_NAME')->first();

        $pensionName = PensionTrans::select('FULL_NAME')->where('MEMBER_NO', $member_no)->pluck('FULL_NAME')->first();

        /**
         * need to compare the portfolios in the accounts
         * they all belong to either moneymarket or pension
         * they are all arrays
         */
        if(count($transPortfolio) > 0 && count($pensionPortfolio) > 0){
                
            $collectedPensionName = collect(str_split(preg_replace("/[^a-zA-Z0-9]/", "", strtolower($pensionName))));
            $collectedTransName = collect(str_split(preg_replace("/[^a-zA-Z0-9]/", "", strtolower($transName))));
            $collectedName = collect(str_split(preg_replace("/[^a-zA-Z0-9]/", "", strtolower($name))));

            $diffPension =  count($collectedPensionName->diff($collectedName));
            $diffTrans =  count($collectedTransName->diff($collectedName));
            
            if(($diffTrans == 0) && ($diffPension == 0)){
            
            }
            else if($diffTrans == 0){

                $index = $sessionPortfolio[0];
                $sequence = $sessionPortfolio[1];

                $matchedPortfolios = [];

                $result = [];

                foreach($transPortfolio as $portfl){
                    if($portfl== $index){
                        $matchedPortfolios[] = $index;
                    }else if($portfl == $sequence){
                        $matchedPortfolios[] = $sequence;
                    }
                }
                $data = MoneyMarketResource::collection(Member::where('PIN_NO', auth()->user()->id_no)->get()); 
                $result[] = collect($data->first())->forget('PORTFOLIO')
                                            ->put('PORTFOLIO', $matchedPortfolios[0])
                                            ->put('accountPurpose', $accountPurpose);
                $result[] = collect($data->first())->forget('PORTFOLIO')
                                        ->put('PORTFOLIO', $matchedPortfolios[1])
                                        ->put('accountPurpose', $accountPurpose);

                return array (
                'money_market' => $result,
                'pension' => []
               ); 

            }else if($diffPension == 0){

                $index = $sessionPortfolio[0];
                $sequence = $sessionPortfolio[1];

                $matchedPortfolios = [];

                $result = [];


                foreach($pensionPortfolio as $portfl){
                    if($portfl== $index){
                        $matchedPortfolios[] = $index;
                    }else if($portfl == $sequence){
                        $matchedPortfolios[] = $sequence;
                    }
                }
                $data = PensionResource::collection(PensionMember::where('ID_NO', auth()->user()->id_no)->get()); 
                $base = collect($data->first());  
                 
                $result[] = collect($data->first())->forget('PORTFOLIO')
                                                    ->put('PORTFOLIO', $matchedPortfolios[0])
                                                    ->put('accountPurpose', $accountPurpose);
                $result[] = collect($data->first())->forget('PORTFOLIO')
                                                ->put('PORTFOLIO', $matchedPortfolios[1])
                                                ->put('accountPurpose', $accountPurpose);

                return array (
                'money_market' => [],
                'pension' => $result
               );
            }

        }else if(count($transPortfolio) > 0){

            $index = $sessionPortfolio[0];
            $sequence = $sessionPortfolio[1];

            $matchedPortfolios = [];

            $result = [];

            foreach($transPortfolio as $portfl){
                if($portfl== $index){
                    $matchedPortfolios[] = $index;
                }else if($portfl == $sequence){
                    $matchedPortfolios[] = $sequence;
                }
            }
            $data = MoneyMarketResource::collection(Member::where('PIN_NO', auth()->user()->id_no)->get()); 
            $result[] = collect($data->first())->forget('PORTFOLIO')
                                        ->put('PORTFOLIO', $matchedPortfolios[0])
                                        ->put('accountPurpose', $accountPurpose);
            $result[] = collect($data->first())->forget('PORTFOLIO')
                                    ->put('PORTFOLIO', $matchedPortfolios[1])
                                    ->put('accountPurpose', $accountPurpose);

            return array (
            'money_market' => $result,
            'pension' => []
           );


        }else if(count($pensionPortfolio) > 0){

            $index = $sessionPortfolio[0];
            $sequence = $sessionPortfolio[1];

            $matchedPortfolios = [];

            $result = [];


            foreach($pensionPortfolio as $portfl){
                if($portfl== $index){
                    $matchedPortfolios[] = $index;
                }else if($portfl == $sequence){
                    $matchedPortfolios[] = $sequence;
                }
            }
            $data = PensionResource::collection(PensionMember::where('ID_NO', auth()->user()->id_no)->get()); 
            $base = collect($data->first());  
             
            $result[] = collect($data->first())->forget('PORTFOLIO')
                                                ->put('PORTFOLIO', $matchedPortfolios[0])
                                                ->put('accountPurpose', $accountPurpose);
            $result[] = collect($data->first())->forget('PORTFOLIO')
                                            ->put('PORTFOLIO', $matchedPortfolios[1])
                                            ->put('accountPurpose', $accountPurpose);

            return array (
            'money_market' => [],
            'pension' => $result
           );

        }

    }

    private function _resolveForMultipleAcc(){

        $result =  array (
            'money_market' => [],
            'pension' => []
        );

    

        foreach($this->_multipleAccount as $acc){


            $utilAcc = collect($acc);

            $member_no = $utilAcc->get('MEMBER_NO');

            $name = $utilAcc->get('name');

            $accountPurpose = $utilAcc->get('accountPurpose');

            $sessionPortfolio = $utilAcc->get('portfolio');

            $transName = Trans::select('FULL_NAME')->where('MEMBER_NO', $member_no)->pluck('FULL_NAME')->first();

            $pensionName = PensionTrans::select('FULL_NAME')->where('MEMBER_NO', $member_no)->pluck('FULL_NAME')->first();

            if(count($sessionPortfolio) == 1){
                /**
                 * single porfolio
                 * 
                 */
                

                $transPortfolio = Trans::select('PORTFOLIO')->distinct()->where('MEMBER_NO', $member_no)->get()->pluck('PORTFOLIO')->first();
                
                $pensionPortfolio = pensionTrans::select('PORTFOLIO')->distinct()->where('MEMBER_NO', $member_no)->get()->pluck('PORTFOLIO')->first();
    

               
                if($transPortfolio == $sessionPortfolio[0]){
                    $data = MoneyMarketResource::collection(Member::where('PIN_NO', auth()->user()->id_no)->get()); 
                    
                    foreach($data as $allAcc){
                        $singleAcc = collect($allAcc);

                        if($singleAcc->get('MEMBER_NO') == $member_no){
                            $result['money_market'][] = collect($allAcc)->forget('PORTFOLIO')
                                                            ->put('PORTFOLIO', $sessionPortfolio[0])
                                                            ->put('accountPurpose', $accountPurpose);
                            break;

                        }
                    } 


                }else if($pensionPortfolio == $sessionPortfolio[0]){
                    $data = PensionResource::collection(PensionMember::where('ID_NO', auth()->user()->id_no)->get());  
                    
                    foreach($data as $allAcc){
                        $singleAcc = collect($allAcc);

                        if($singleAcc->get('MEMBER_NO') == $member_no){
                            $result['pension'][] = collect($allAcc)->forget('PORTFOLIO')
                                                            ->put('PORTFOLIO', $sessionPortfolio[0])
                                                            ->put('accountPurpose', $accountPurpose);
                            break;

                        }
                    } 

                }
            

            }else if(count($sessionPortfolio) > 1){
                
                /**
                 * multiple portfolio
                 */

                $transPortfolio = Trans::select('PORTFOLIO')->distinct()->where('MEMBER_NO', $member_no)->get()->pluck('PORTFOLIO');
            
                $pensionPortfolio = pensionTrans::select('PORTFOLIO')->distinct()->where('MEMBER_NO', $member_no)->get()->pluck('PORTFOLIO');
            
                /**
                 * need to compare the portfolios in the accounts
                 * they all belong to either moneymarket or pension
                 * they are all arrays
                 */
                if(count($transPortfolio) > 0 && count($pensionPortfolio) > 0){
                    
                    $collectedPensionName = collect(str_split(preg_replace("/[^a-zA-Z0-9]/", "", strtolower($pensionName))));
                    $collectedTransName = collect(str_split(preg_replace("/[^a-zA-Z0-9]/", "", strtolower($transName))));
                    $collectedName = collect(str_split(preg_replace("/[^a-zA-Z0-9]/", "", strtolower($name))));
        
                    $diffPension =  count($collectedPensionName->diff($collectedName));
                    $diffTrans =  count($collectedTransName->diff($collectedName));
                    
                    if(($diffTrans == 0) && ($diffPension == 0)){
                    
                    }
                    else if($diffTrans == 0){

                        $index = $sessionPortfolio[0];
                        $sequence = $sessionPortfolio[1];
    
                        $matchedPortfolios = [];
    
    
                        foreach($transPortfolio as $portfl){
                            if($portfl== $index){
                                $matchedPortfolios[] = $index;
                            }else if($portfl == $sequence){
                                $matchedPortfolios[] = $sequence;
                            }
                        }
                        
                        $data = MoneyMarketResource::collection(Member::where('PIN_NO', auth()->user()->id_no)->get());
                        foreach($data as $allAcc){
                            $singleAcc = collect($allAcc);

                            if($singleAcc->get('MEMBER_NO') == $member_no){
                                $result['money_market'][] = collect($allAcc)->forget('PORTFOLIO')
                                                                ->put('PORTFOLIO', $matchedPortfolios[0])
                                                                ->put('accountPurpose', $accountPurpose);
                                $result['money_market'][] = collect($allAcc)->forget('PORTFOLIO')
                                                                ->put('PORTFOLIO', $matchedPortfolios[1])
                                                                ->put('accountPurpose', $accountPurpose);
                                break;

                            }
                        } 

                    }else if($diffPension == 0){
                        $index = $sessionPortfolio[0];
                        $sequence = $sessionPortfolio[1];
    
                        $matchedPortfolios = [];
    
    
                        foreach($pensionPortfolio as $portfl){
                            if($portfl== $index){
                                $matchedPortfolios[] = $index;
                            }else if($portfl == $sequence){
                                $matchedPortfolios[] = $sequence;
                            }
                        }
                        $data = PensionResource::collection(PensionMember::where('ID_NO', auth()->user()->id_no)->get()); 
                        foreach($data as $allAcc){
                            $singleAcc = collect($allAcc);

                            if($singleAcc->get('MEMBER_NO') == $member_no){
                                $result['pension'][] = collect($allAcc)->forget('PORTFOLIO')
                                                                ->put('PORTFOLIO', $matchedPortfolios[0])
                                                                ->put('accountPurpose', $accountPurpose);
                                $result['pension'][] = collect($allAcc)->forget('PORTFOLIO')
                                                                ->put('PORTFOLIO', $matchedPortfolios[1])
                                                                ->put('accountPurpose', $accountPurpose);
                                break;

                            }
                        } 
                    }
        
                }
                else if(count($transPortfolio) > 0){

                    $index = $sessionPortfolio[0];
                    $sequence = $sessionPortfolio[1];

                    $matchedPortfolios = [];


                    foreach($transPortfolio as $portfl){
                        if($portfl== $index){
                            $matchedPortfolios[] = $index;
                        }else if($portfl == $sequence){
                            $matchedPortfolios[] = $sequence;
                        }
                    }
                    
                    $data = MoneyMarketResource::collection(Member::where('PIN_NO', auth()->user()->id_no)->get());
                    foreach($data as $allAcc){
                        $singleAcc = collect($allAcc);

                        if($singleAcc->get('MEMBER_NO') == $member_no){
                            $result['money_market'][] = collect($allAcc)->forget('PORTFOLIO')
                                                            ->put('PORTFOLIO', $matchedPortfolios[0])
                                                            ->put('accountPurpose', $accountPurpose);
                            $result['money_market'][] = collect($allAcc)->forget('PORTFOLIO')
                                                            ->put('PORTFOLIO', $matchedPortfolios[1])
                                                            ->put('accountPurpose', $accountPurpose);
                            break;

                        }
                    } 



                }else if(count($pensionPortfolio) > 0){
                    $index = $sessionPortfolio[0];
                    $sequence = $sessionPortfolio[1];

                    $matchedPortfolios = [];


                    foreach($pensionPortfolio as $portfl){
                        if($portfl== $index){
                            $matchedPortfolios[] = $index;
                        }else if($portfl == $sequence){
                            $matchedPortfolios[] = $sequence;
                        }
                    }
                    $data = PensionResource::collection(PensionMember::where('ID_NO', auth()->user()->id_no)->get()); 
                    foreach($data as $allAcc){
                        $singleAcc = collect($allAcc);

                        if($singleAcc->get('MEMBER_NO') == $member_no){
                            $result['pension'][] = collect($allAcc)->forget('PORTFOLIO')
                                                            ->put('PORTFOLIO', $matchedPortfolios[0])
                                                            ->put('accountPurpose', $accountPurpose);
                            $result['pension'][] = collect($allAcc)->forget('PORTFOLIO')
                                                            ->put('PORTFOLIO', $matchedPortfolios[1])
                                                            ->put('accountPurpose', $accountPurpose);
                            break;

                        }
                    } 

                }

            }

        
        }

        return $result;
    }

    public function authInfo(){
        return auth()->user();
        
    }

    public function getTraversingAccounts(){
        // $pension = PensionMember::select('ID_NO')->get();
        // $moneyMarket = Member::select('PIN_NO')->get();

        // return collect(collect($moneyMarket->pluck('PIN_NO'))->intersect($pension->pluck('ID_NO')))->filter(function ($value, $key) {
        //     return is_numeric($value);
        // });

        $memberNosBf =  Trans::select('MEMBER_NO')->where('PORTFOLIO', 'Balanced Fund')->get();
        $matched = [];
        foreach($memberNosBf as $member){
            $member_no = collect($member)->get('MEMBER_NO');
            $member_no = Trans::select('MEMBER_NO')->where('PORTFOLIO', 'Money Market')
                                        ->where('MEMBER_NO', $member_no)
                                        ->first();

            if($member_no){
                $matched[] = $member;
            }
        }
        $moneyMarket = Member::select('PIN_NO','MEMBER_NO')->get();
        
        return collect($moneyMarket->intersect($matched)->pluck('PIN_NO'))->filter(function($value){
            return is_numeric($value);
        });

    
    }
}
