<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/webhook-stripe',
        '/webhook-paypal',
        'plan/paytm/*',
        //  '*/invoice/paytm/*',
        '*/invoice-pay-with-paymentwall/*',
        'iyzipay/callback/*',
        '*/invoice/iyzipay/*',
        '*/aamarpay*',
        '*/paytab-success/*',
        '/cinetpay/payment',
        'plan-cinetpay-return',
        '/invoice-cinetpay-return/*',
        '/client/invoice-cinetpay-return/*'
    ];
    // $except = ['*cancel-payment*'];
}
