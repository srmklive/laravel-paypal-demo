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

Route::get('/', 'PayPalController@getIndex');
Route::get('paypal/ec-checkout', 'PayPalController@getExpressCheckout');
Route::get('paypal/ec-checkout-success', 'PayPalController@getExpressCheckoutSuccess');
Route::get('paypal/adaptive-pay', 'PayPalController@getAdaptivePay');
Route::post('paypal/notify', 'PayPalController@notify');

Auth::routes();
