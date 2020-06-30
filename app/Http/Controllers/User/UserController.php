<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Bulwark\Member;
use App\Pension\Member as PensionMember;
use App\Http\Resources\Money_Market as MoneyMarketResource;
use App\Http\Resources\Pension as PensionResource;

class UserController extends Controller
{
    public function index()
    {
        

        return array (
            'money_market' => MoneyMarketResource::collection(Member::limit(100)->get()),
            'pension' => PensionResource::collection(PensionMember::limit(100)->get())
        );


    }
}
