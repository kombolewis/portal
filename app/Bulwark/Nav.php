<?php

namespace App\Bulwark;

use Illuminate\Database\Eloquent\Model;

class Nav extends Model
{
    protected $connection = 'firebird';

    protected $table = "NAVS";
}
