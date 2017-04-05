@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <?php if(!empty($response['code'])) { ?>
                <div class="alert alert-<?php echo $response['code']; ?>">
                    <?php echo $response['message']; ?>
                </div>
                <?php } ?>
                <div class="panel panel-default">
                    <div class="panel-heading">Laravel PayPal Demo</div>
                    <div class="panel-body">
                        <ul>
                            <li><a href="{{url('paypal/ec-checkout')}}">Express Checkout</a></li>
                            <li><a href="{{url('paypal/ec-checkout?mode=recurring')}}">Create Recurring Payments Profile</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection