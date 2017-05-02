<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;

class StripeConnectController extends Controller
{
    private $token_uri= 'https://connect.stripe.com/oauth/token';
    private $authorize_uri = 'https://connect.stripe.com/oauth/authorize';

    public function connect()
    {
        $code = request('code');
        $error=request('error');
        $api_key = config('services.stripe.secret');

        $client_id = config('services.stripe.client_id');

        if (isset($code)) { // Redirect w/ code
            $token_request_body = array(
                'client_secret' => $api_key,
                'grant_type' => 'authorization_code',
                'client_id' => $client_id,
                'code' => $code,
            );
            $req = curl_init($this->token_uri);
            curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($req, CURLOPT_POST, true);
            curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
            // TODO: Additional error handling
            $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
            $resp = json_decode(curl_exec($req), true);
            curl_close($req);
           
            return redirect()->back()->with("you succesfully connected My platform");
        } else if (isset($error)) { // Error

            return redirect()->back()->withErrors("you declined to connect My platform");
        } else { // Show OAuth link
            $authorize_request_body = array(
                'response_type' => 'code',
                'scope' => 'read_write',
                'client_id' => $client_id
            );
            $url = $this->authorize_uri . '?' . http_build_query($authorize_request_body);
            return "<a href='$url'>Connect with Stripe</a>";

        }
    }
}

