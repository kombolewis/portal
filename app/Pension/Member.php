<?php

namespace App\Pension;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $connection = 'firebird2';
    
    protected $table = "MEMBERS";
}
