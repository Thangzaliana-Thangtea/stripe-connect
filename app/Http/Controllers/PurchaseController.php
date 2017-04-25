<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\Error\InvalidRequest;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Stripe\Transfer;

class PurchaseController extends Controller
{
    public function store(){
        Stripe::setApiKey(config('services.stripe.secret'));

        try{
            $customer=Customer::create([
                'email'=>request('stripeEmail'),
                'source'=>request('stripeToken')
            ]);

            $charge=Charge::create([
                'customer'=>$customer,
                'amount'=>8000,
                'currency'=>'usd'
            ]);

            $fees=$charge->amount*(3.275/100);
            $moneyToTransfer=($charge->amount-$fees)/2;

            $transfer1 = Transfer::create(array(
                'amount' => $moneyToTransfer,
                'currency' => 'usd',
                'destination' => 'acct_1AC1fYKemdBI3F38',
                'transfer_group' => 1,
            ));
            $transfer2 = Transfer::create(array(
                'amount' => $moneyToTransfer,
                'currency' => 'usd',
                'destination' => 'acct_161uTUBLhEhjnbf3',
                'transfer_group' => 1,
            ));
            return redirect()->back()->with('success','successfully buy an item');
        }catch (\Exception $e){
            return redirect()->back()->withErrors('failure transaction due to '.$e->getMessage());
        }


        return 'done';
    }
    public function deposit(){
        Stripe::setApiKey(config('services.stripe.secret'));

        $customer=Customer::create([
            'email'=>request('stripeEmail'),
            'source'=>request('stripeToken')
        ]);

        $charge=Charge::create([
            'customer'=>$customer,
            'amount'=>10000,
            'currency'=>'usd'
        ]);
        return redirect()->back()->with('success','$100 is deposited');
    }

}
