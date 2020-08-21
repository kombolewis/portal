<?php

namespace App\Pension;

use Illuminate\Database\Eloquent\Model;

class Nav extends Model
{
    protected $connection = 'firebird2';
    
    protected $table = "NAVS";
}
