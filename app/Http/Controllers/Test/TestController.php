<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Bulwark\Members;
use App\Pension\Members as PensionMembers;
use App\Http\Resources\Money_Market as MoneyMarketResource;
use App\Http\Resources\Pension as PensionResource;

class TestController extends Controller
{
    public function index()
    {
        

        return array (
            'money_market' => MoneyMarketResource::collection(Members::limit(100)->get()),
            'pension' => PensionResource::collection(PensionMembers::limit(100)->get())
        );


    }
}
