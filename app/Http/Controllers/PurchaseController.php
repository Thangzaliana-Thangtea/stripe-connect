<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\Balance;
use Stripe\Error\InvalidRequest;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Stripe\Transfer;

class PurchaseController extends Controller
{
    public function store()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $amount = request('price')*10;
        $balance = Balance::retrieve();
        $available = $balance->available[0]->amount;
    /*
        $customer = Customer::create([
            'email' => request('stripeEmail'),
            'source' => request('stripeToken')
        ]);
    */
        try {
            $token = request('stripeToken');
            // Create a Charge by method Direct charge:
            $charge = Charge::create(array(
                "amount" => $amount,
                "currency" => "usd",
                "source" => $token,
                "application_fee" => 10,
            ), array("stripe_account" => "acct_1AC1fYKemdBI3F38"));

         return redirect()->back()->with('success','transaction successfully done');
        }catch(\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            return redirect()->back()->withErrors('failure transaction due to : '.$e->getMessage());
        } catch (\Stripe\Error\RateLimit $e) {
            // Too many requests made to the API too quickly
            return redirect()->back()->withErrors('failure transaction due to : '.$e->getMessage());
        } catch (\Stripe\Error\InvalidRequest $e) {
            // Invalid parameters were supplied to Stripe's API
            return redirect()->back()->withErrors('failure transaction due to : '.$e->getMessage());
        } catch (\Stripe\Error\Authentication $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            return redirect()->back()->withErrors('failure transaction due to : '.$e->getMessage());
        } catch (\Stripe\Error\ApiConnection $e) {
            // Network communication with Stripe failed
            return redirect()->back()->withErrors('failure transaction due to : '.$e->getMessage());
        } catch (\Stripe\Error\Base $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            return redirect()->back()->withErrors('failure transaction due to : '.$e->getMessage());
        }
        catch (\Exception $e) {
            return redirect()->back()->withErrors('failure transaction due to : '.$e->getMessage());
        }

/*

         $fees=$charge->amount*(3.275/100);
         $moneyToTransfer=($charge->amount-$fees)/2;

         if ($available<$moneyToTransfer )
             return redirect()->back()->withErrors("Money transfer to connected account error: due to insufficient balance");

         $transfer1 = Transfer::create(array(
             'amount' => $moneyToTransfer,
             'currency' => 'usd',
             'destination' => 'acct_1AC1fYKemdBI3F38',
             'transfer_group' => 1,
         ));

     }catch (\Exception $e){
     }

*/
    }

    public function deposit()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $customer = Customer::create([
            'email' => request('stripeEmail'),
            'source' => request('stripeToken')
        ]);

        $charge = Charge::create([
            'customer' => $customer,
            'amount' => 10000,
            'currency' => 'usd'
        ]);
        return redirect()->back()->with('success', '$100 is deposited');
    }

}
