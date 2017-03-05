<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Srmklive\PayPal\Traits\IPNResponse;

class PayPalController extends Controller
{
    use IPNResponse;

    public function getIndex(Request $request)
    {
        $response = [];
        if (session()->has('code')) {
            $response['code'] = session()->get('code');
            session()->forget('code');
        }

        if (session()->has('message')) {
            $response['message'] = session()->get('message');
            session()->forget('message');
        }

        return view('welcome', compact('response'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getExpressCheckout(Request $request)
    {
        $cart = $this->getCheckoutData();

        $response = express_checkout()->setExpressCheckout($cart);
        if (!empty($response['paypal_link'])) {
            return redirect($response['paypal_link']);
        }
    }

    /**
     * Process payment on PayPal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getExpressCheckoutSuccess(Request $request)
    {
        $token = $request->get('token');
        $PayerID = $request->get('PayerID');

        $cart = $this->getCheckoutData();

        // Verify Express Checkout Token
        $response = express_checkout()->getExpressCheckoutDetails($token);
        if(in_array(strtoupper($response['ACK']), ['SUCCESS','SUCCESSWITHWARNING'])) {
            // Perform transaction on PayPal
            $payment_status = express_checkout()->doExpressCheckoutPayment($cart, $token, $PayerID);
            $status = $payment_status['PAYMENTINFO_0_PAYMENTSTATUS'];

            $invoice = new Invoice;
            $invoice->title = $cart['invoice_description'];
            $invoice->price = $cart['total'];
            if (!strcasecmp($status, 'Completed') || !strcasecmp($status, 'Processed')) {
                $invoice->paid = 1;
            } else {
                $invoice->paid = 0;
            }
            $invoice->save();

            collect($cart['items'])->each(function($product) use($invoice) {
                $item = new Item;
                $item->invoice_id = $invoice->id;
                $item->item_name = $product['name'];
                $item->item_price = $product['price'];
                $item->item_qty = $product['qty'];

                $item->save();
            });

            if ($invoice->paid) {
                session()->put(["code" => "success", "message" => "Order $invoice->id has been paid successfully!"]);
            } else {
                session()->put(["code" => "danger", "message" => "Error processing PayPal payment for Order $invoice->id!"]);
            }

            return redirect('/');
        }
    }

    /**
     * Parse PayPal IPN.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function notify(Request $request)
    {
        $request->merge(['cmd' => '_notify-validate']);
        $post = $request->all();

        $response = $this->verifyIPN($post);

        $logFile = "ipn_log_".Carbon::now()->format("Ymd_His").".txt";
        Storage::disk('local')->put($logFile, $response);
    }

    /**
     * Set cart data for processing payment on PayPal.
     *
     * @return array
     */
    protected function getCheckoutData()
    {
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

        $order_id = Invoice::all()->count() + 1;

        $data['invoice_id'] = 'PAYPALDEMOAPP_'.$order_id;
        $data['invoice_description'] = "Order #$order_id Invoice";
        $data['return_url'] = url('/paypal/ec-checkout-success');
        $data['cancel_url'] = url('/paypal/ec-checkout');

        $total = 0;
        foreach($data['items'] as $item) {
            $total += $item['price']*$item['qty'];
        }

        $data['total'] = $total;

        return $data;
    }
}
