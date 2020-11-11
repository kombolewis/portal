<?php

namespace App\AfricasTalking;

use AfricasTalking\SDK\AfricasTalking;

class Sms
{
    
    private $_username = '';
    private $_apiKey = '';
    private $_AT;


    public function __construct(){
        $this->_AT = new AfricasTalking($this->_username, $this->_apiKey);
    }

    public function sendSMS($tel, $msg){
        return $this->_AT->sms()->send([
            'to' => $tel,
            'message'  => $msg,
            'from' => 'Zimele_UT'
        ]);
    }
}
