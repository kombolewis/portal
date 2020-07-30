<?php

namespace App\Pension;

use Illuminate\Database\Eloquent\Model;

class Trans extends Model
{
    protected $connection = 'firebird2';
    
    protected $table = "TRANS";
}
