<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});
Route::get('stripe-connect','StripeConnectController@connect');

Route::post('purchase','PurchaseController@store');
Route::post('deposit','PurchaseController@deposit');
