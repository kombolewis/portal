<?php

namespace App\Bulwark;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $connection = 'firebird';

    protected $table = "MEMBERS";
    

}
