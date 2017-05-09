<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\Account;
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
        //Direct change
        Stripe::setApiKey(config('services.stripe.secret'));
        try {
            $balance = Balance::retrieve();
            $available = $balance->available[0]->amount;
            $moneyToTransfer = 300000;

            //Create customer
            $customer = Customer::create([
                'email' => request('stripeEmail'),
                'source' => request('stripeToken')
            ]);

            //changing customer of $100

            $charge = Charge::create([
                'customer' => $customer,
                'amount' => $moneyToTransfer,
                'currency' => 'usd',
                'description' => 'charge description'
            ]);

            //getting all connected accounts

            $allConnectedAccount = Account::all()->data;

            // split money into account by business rule
            $splitMoney=$moneyToTransfer*10/100;

            //transfer money to each connected account
            foreach ($allConnectedAccount as $connectedAccount) {

                //if the platform account holder balance is insufficient
                if ($connectedAccount->id == 'acct_19zUkVKY1CPCCEPW') {
                    break;
                }

                if ($available < $moneyToTransfer) {

                    //the transfer request succeeds regardless of your available balance and the transfer
                    // itself only occurs once the charge’s funds become available.

                    $transfer = \Stripe\Transfer::create(array(
                        "amount" => $splitMoney,
                        "currency" => "usd",
                        "source_transaction" => $charge->id,
                        "destination" => $connectedAccount->id,
                    ));
                    //TODO:save transfer object into database for later use
                } else {

                    $transfer = Transfer::create(array(
                        'amount' => $splitMoney,
                        'currency' => 'usd',
                        'destination' => $connectedAccount->id,
                        'transfer_group' => 1,
                    ));
                    //TODO:save transfer object into database for later use
                }
            }

            return redirect()->back()->with('success', 'transaction successfully done');
        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            return redirect()->back()->withErrors('failure transaction due to : ' . $e->getMessage());
        } catch (\Stripe\Error\RateLimit $e) {
            // Too many requests made to the API too quickly
            return redirect()->back()->withErrors('failure transaction due to : ' . $e->getMessage());
        } catch (\Stripe\Error\InvalidRequest $e) {
            // Invalid parameters were supplied to Stripe's API
            return redirect()->back()->withErrors('failure transaction due to : ' . $e->getMessage());
        } catch (\Stripe\Error\Authentication $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            return redirect()->back()->withErrors('failure transaction due to : ' . $e->getMessage());
        } catch (\Stripe\Error\ApiConnection $e) {
            // Network communication with Stripe failed
            return redirect()->back()->withErrors('failure transaction due to : ' . $e->getMessage());
        } catch (\Stripe\Error\Base $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            return redirect()->back()->withErrors('failure transaction due to : ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('failure transaction due to : ' . $e->getMessage());
        }


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

    public function doDirectCharge()
    {
        /* Create a Charge by method Direct charge:
        $charge = Charge::create(array(
            "amount" => 1000,
            "currency" => "usd",
            "source" => $token,
            "application_fee" => 10,
        ), array("stripe_account" => "acct_1AC1fYKemdBI3F38"));*/
    }
}
