<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function all_customer(){
        $customer = Customer::orderBy('id','desc')->get();
        return response()->json([
            'customers' =>$customer
        ],200);


    }
}
