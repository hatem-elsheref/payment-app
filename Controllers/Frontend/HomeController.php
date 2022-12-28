<?php

namespace Controllers\Frontend;

use Framework\Application;
use Framework\View;
use Models\Order;
use Models\OrderItems;
use Models\Product;
use Stripe\StripeClient;

class HomeController
{

    public function index()
    {
        return View::renderView('Frontend.home', false);
    }

    public function products()
    {
        $product = new Product();
        $products =  $product->getAll();
        return View::renderView('Frontend.products', false, compact('products'));
    }


    public function showCheckoutForm($product)
    {
        $product = (new Product())->find($product);
        $cart = [$product];
        return View::renderView('Frontend.checkout', false, compact('cart'));
    }

    public function checkout()
    {
        // here we create order and order items
        // then redirect to payment page to continue billing
        // save the order id that needed to continue billing in session
        // make order billing status pending

        $order = new Order();
        $status = $order->insert([
            "user_id"           => null,
            "address"           => request("address"),
            "phone"             => request("phone"),
            "email"             => request("email"),
            "payment_status"    => Order::PENDING_STATUS,
            "shipping_address"  => json_encode([
                "country" => request("country"),
                "name"    => request("firstname") . request("lastname"),
                "state"   => request("state") ,
                "zip"   => request("zip") ,
            ])
        ]);

        if ($status != false && gettype($status) === 'string' && (int) $status > 0){
            $orderItems = new OrderItems();
            foreach (request('products') as $productId => $productQuantity){
                $orderItems->insert([
                    "quantity"     => $productQuantity,
                    "product_id"   => $productId,
                    "order_id"     => $status,
                ]);
            }

            session_set("order_id", $status);
            flash("Success", "Success Order! Waiting To Complete Your Payment");
            return redirectTo("/processing-to-payment");
        }
        flash("Error", "Failed Order!");
        redirectTo("/");
    }

    public function process($redirectTo = "/pay")
    {
        if (session_has('order_id') && !is_null(session('order_id')))
            return view("Frontend.waitingBilling", ["to" => $redirectTo]);

        return redirectTo('/');
    }


    public function completePayment()
    {
        if (!session_has('order_id') || is_null(session('order_id')))
            return redirectTo('/');

        $data = ["order" => session('order_id')];
        return view('Frontend.payment', compact('data'));
    }
    public function createStripePaymentIntent()
    {
        // here we check if this :
        //  order exists or not
        // order belongs to current auth user or not - or current session has order_id stored
        // order status  pending or completed or canceled or failed
        // from order we get currency - price of item * amount for each items in this order

        $orderId = request('order_id');

        $order = new Order();
        $order = $order->find($orderId, 'id', [["payment_status", "=", Order::PENDING_STATUS]]);
        if (!$order || $orderId != session('order_id')){
            http_response_code(400);
            return json_encode(['data' => null, 'message' => 'order Not Found', 'code' => 400]);
        }

        $orderItems = new OrderItems();
        $orderItems = $orderItems->getAll("*", [["order_id", "=", $order->id]]);

        $currency = "usd";
        $price    = 0;

        $product = new Product();

        foreach ($orderItems as $item){
            $productData = $product->find($item->product_id);
            if ($productData){
                $price+= ((int) $productData->price * $item->quantity);
            }
        }

        \Stripe\Stripe::setApiKey(config('app.services.stripe.secret_key'));

        // Create a PaymentIntent with amount and currency
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount'   => $price,
            'currency' => $currency,
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        header('Content-Type: application/json');
        return json_encode(["clientSecret" => $paymentIntent->client_secret]);
    }

    public function paymentCallback($orderId)
    {
        $order = new Order();
        $orderData = $order->find($orderId, 'id', [["payment_status", "=", Order::PENDING_STATUS]]);
        if (is_null($orderData) || is_null($orderId) || empty(request('payment_intent'))){
            flash("Error", "Un Defined Order!");
        }else{
            $stripe = new StripeClient(config('app.services.stripe.secret_key'));
            $stripeResponse = $stripe->paymentIntents->retrieve(request('payment_intent'));

            if ($stripeResponse->status === "succeeded"){
                // update order status here and return success message back to user
                session_remove("order_id");
                $updateStatus = $order->update(
                    ["payment_status" => Order::COMPLETED_STATUS, "payment_intent" => request('payment_intent')]
                ,$orderData->id);

                if ($updateStatus)
                    flash("Success", "Success Operation #number#" . request('payment_intent'));
                else
                    flash("Success", "Success, But Failed To Save Your Order In Our System");
            }else{
                flash("Error", "Failed Billing Operation!");
            }
        }

        return view("Frontend.billingResponse");
    }
}
