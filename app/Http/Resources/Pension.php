<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Pension extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'MEMBER_NO' => mb_convert_encoding($this->MEMBER_NO, "UTF-8", "UTF-8"),                  
            'TITLE' => mb_convert_encoding($this->TITLE, "UTF-8", "UTF-8"),                  
            'FIRSTNAME' => mb_convert_encoding($this->FIRSTNAME, "UTF-8", "UTF-8"),                      
            'SURNAME' => mb_convert_encoding($this->SURNAME, "UTF-8", "UTF-8"),                      
            'LASTNAME' => mb_convert_encoding($this->LASTNAME, "UTF-8", "UTF-8"),                        
            'OTHERNAMES' => mb_convert_encoding($this->OTHERNAMES, "UTF-8", "UTF-8"),               
            'ALLNAMES' => mb_convert_encoding($this->ALLNAMES, "UTF-8", "UTF-8"),                      
            'POST_ADDRESS' => mb_convert_encoding($this->POST_ADDRESS, "UTF-8", "UTF-8"),                  
            'REG_DATE' => mb_convert_encoding($this->REG_DATE, "UTF-8", "UTF-8"),                        
            'TEL_NO' => mb_convert_encoding($this->TEL_NO, "UTF-8", "UTF-8"),                         
            'PHYS_ADDRESS' => mb_convert_encoding($this->PHYS_ADDRESS, "UTF-8", "UTF-8"),                                             
            'TOWN' => mb_convert_encoding($this->TOWN, "UTF-8", "UTF-8"),                                             
            'HSE_NO' => mb_convert_encoding($this->HSE_NO, "UTF-8", "UTF-8"),                                             
            'COUNTRY' => mb_convert_encoding($this->COUNTRY, "UTF-8", "UTF-8"),                                             
            'SMS_NTFY' => mb_convert_encoding($this->SMS_NTFY, "UTF-8", "UTF-8"),                        
            'SMSQRY_ACCEPT' => mb_convert_encoding($this->SMSQRY_ACCEPT, "UTF-8", "UTF-8"),                   
            'GSM_NO' => mb_convert_encoding($this->GSM_NO, "UTF-8", "UTF-8"),                         
            'E_MAIL' => mb_convert_encoding($this->E_MAIL, "UTF-8", "UTF-8"),                         
            'ID_NO' => mb_convert_encoding($this->ID_NO, "UTF-8", "UTF-8"),                          
            'PIN_NO' => mb_convert_encoding($this->PIN_NO, "UTF-8", "UTF-8"),                    
            'DOB' => mb_convert_encoding($this->DOB, "UTF-8", "UTF-8"),                             
            'GENDER' => mb_convert_encoding($this->GENDER, "UTF-8", "UTF-8"),                          
            'MARITALSTATUS' => mb_convert_encoding($this->MARITALSTATUS, "UTF-8", "UTF-8"),                
            'COMPANY_NO' => mb_convert_encoding($this->COMPANY_NO, "UTF-8", "UTF-8"),                
            'COMPANYNAME' => mb_convert_encoding($this->COMPANYNAME, "UTF-8", "UTF-8"),                
            'DATEJOINEDP' => mb_convert_encoding($this->DATEJOINEDP, "UTF-8", "UTF-8"),                
            'CONFIRMEDDATE' => mb_convert_encoding($this->CONFIRMEDDATE, "UTF-8", "UTF-8"),                
        ];
    }
}
