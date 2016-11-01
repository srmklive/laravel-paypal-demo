<?php

namespace App\Http\Controllers;

use App\Invoice;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    /**
     * @param Request $request
     */
    public function getExpressCheckout(Request $request)
    {
        $cart = $this->getCheckoutData();

        $response = express_checkout()->setExpressCheckout($cart);
        if (!empty($response['paypal_link'])) {
            return redirect($response['paypal_link']);
        }
    }


    protected function getCheckoutData()
    {
        if (session()->has('cart')) {
            return session()->get('cart');
        }

        $data = [];
        $data['items'] = [
            [
                'name' => 'Product 1',
                'price' => 9.99,
                'qty' => 1
            ],
            [
                'name' => 'Product 2',
                'price' => 4.99,
                'qty' => 2
            ]
        ];

        $data['invoice_id'] = Invoice::all()->count() + 1;
        $data['invoice_description'] = "Order #$data[invoice_id] Invoice";
        $data['return_url'] = url('/paypal/ec-checkout?mode=success');
        $data['cancel_url'] = url('/paypal/ec-checkout');

        $total = 0;
        foreach($data['items'] as $item) {
            $total += $item['price'];
        }

        $data['total'] = $total;

        session()->put('cart',$data);

        return session()->get('cart');
    }
}
