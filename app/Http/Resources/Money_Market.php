<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Money_Market extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'MEMBER_NO' => mb_convert_encoding($this->MEMBER_NO, "UTF-8", "UTF-8"),                  
            'CUSTOMER_NAME' => mb_convert_encoding($this->CUSTOMER_NAME, "UTF-8", "UTF-8"),                   
            'SURNAME' => mb_convert_encoding($this->SURNAME, "UTF-8", "UTF-8"),                      
            'LASTNAME' => mb_convert_encoding($this->LASTNAME, "UTF-8", "UTF-8"),                        
            'OTHERNAMES' => mb_convert_encoding($this->OTHERNAMES, "UTF-8", "UTF-8"),               
            'ALLNAMES' => mb_convert_encoding($this->ALLNAMES, "UTF-8", "UTF-8"),                      
            'POST_ADDRESS' => mb_convert_encoding($this->POST_ADDRESS, "UTF-8", "UTF-8"),                  
            'REG_DATE' => mb_convert_encoding($this->REG_DATE, "UTF-8", "UTF-8"),                        
            'TEL_NO' => mb_convert_encoding($this->TEL_NO, "UTF-8", "UTF-8"),                         
            'PHYS_ADDRESS' => mb_convert_encoding($this->PHYS_ADDRESS, "UTF-8", "UTF-8"),                                             
            'SMS_NTFY' => mb_convert_encoding($this->SMS_NTFY, "UTF-8", "UTF-8"),                        
            'SMSQRY_ACCEPT' => mb_convert_encoding($this->SMSQRY_ACCEPT, "UTF-8", "UTF-8"),                   
            'GSM_NO' => mb_convert_encoding($this->GSM_NO, "UTF-8", "UTF-8"),                         
            'CELLNO' => mb_convert_encoding($this->CELLNO, "UTF-8", "UTF-8"),                       
            'E_MAIL' => mb_convert_encoding($this->E_MAIL, "UTF-8", "UTF-8"),                         
            'ID_NO' => mb_convert_encoding($this->ID_NO, "UTF-8", "UTF-8"),                          
            'PIN_NO' => mb_convert_encoding($this->PIN_NO, "UTF-8", "UTF-8"),                    
            'CURR_BAL' => mb_convert_encoding($this->CURR_BAL, "UTF-8", "UTF-8"),                  
            'ACCT_TYPE_ID' => mb_convert_encoding($this->ACCT_TYPE_ID, "UTF-8", "UTF-8"),               
            'TITLE' => mb_convert_encoding($this->TITLE, "UTF-8", "UTF-8"),                         
            'TERMINATIONDATE' => mb_convert_encoding($this->TERMINATIONDATE, "UTF-8", "UTF-8"),               
            'VALIDATEACC' => mb_convert_encoding($this->VALIDATEACC, "UTF-8", "UTF-8"),                                                           
            'DOB' => mb_convert_encoding($this->DOB, "UTF-8", "UTF-8"),                             
            'GENDER' => mb_convert_encoding($this->GENDER, "UTF-8", "UTF-8"),                          
            'MARITALSTATUS' => mb_convert_encoding($this->MARITALSTATUS, "UTF-8", "UTF-8"),                
            'DOB1' => mb_convert_encoding($this->DOB1, "UTF-8", "UTF-8"),                            
            'GENDER1' => mb_convert_encoding($this->GENDER1, "UTF-8", "UTF-8"),                         
            'MARITALSTATUS1' => mb_convert_encoding($this->MARITALSTATUS1, "UTF-8", "UTF-8"),               
            'GRPTYPE' => mb_convert_encoding($this->GRPTYPE, "UTF-8", "UTF-8"),                         
            'FIRSTNAME' => mb_convert_encoding($this->FIRSTNAME, "UTF-8", "UTF-8"),                       
        ];

        
    }
}
