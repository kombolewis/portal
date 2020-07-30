<?php

namespace App\Bulwark;

use Illuminate\Database\Eloquent\Model;

class Trans extends Model
{
    protected $connection = 'firebird';

    protected $table = "TRANS";
}
