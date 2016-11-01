@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Laravel PayPal Demo</div>
                    <div class="panel-body">
                        <ul>
                            <li><a href="{{url('paypal/ec-checkout')}}">Express Checkout</a></li>
                            <li><a href="{{url('paypal/ec-checkout-with-recurring')}}">Create Recurring Profile With Express Checkout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
