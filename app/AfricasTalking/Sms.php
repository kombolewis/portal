<?php

namespace App\AfricasTalking;

use AfricasTalking\SDK\AfricasTalking;

class Sms
{
    
    private $_username = 'Zimele_UT';
    private $_apiKey = 'cf3470d7c8c817d6d3f38a946f34dfa7a25fdc9e5252a64261e4b184683595e1';
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
