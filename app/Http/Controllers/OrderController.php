<?php

namespace App\Http\Controllers;

use App\Events\OrderCompletedEvent;
use App\Http\Resources\OrderResource;
use App\Models\Link;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Cartalyst\Stripe\Stripe;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return OrderResource::collection(Order::with('orderItems')->get());
    }

    public function store(Request $request)
    {
        if (!$link = Link::where('code', $request->code)->first()) {
            abort(400, 'Invalid code');
        }

        try {
            \DB::beginTransaction();

            $order = Order::create([
                'code'             => $link->code,
                'user_id'          => $link->user->id,
                'ambassador_email' => $link->user->email,
                'first_name'       => $request->first_name,
                'last_name'        => $request->last_name,
                'email'            => $request->email,
                'address'          => $request->address,
                'country'          => $request->country,
                'zip'              => $request->zip,
                'city'             => $request->city,
            ]);

            foreach ($request->products as $item) {
                $product = Product::find($item['product_id']);

                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_title'      => $product->title,
                    'price'              => $product->price,
                    'quantity'           => $item['quantity'],
                    'ambassador_revenue' => 0.1 * $product->price * $item['quantity'],
                    'admin_revenue'      => 0.9 * $product->price * $item['quantity'],
                ]);

                $lineItems[] = [
                    'name'        => $product->title,
                    'description' => $product->description,
                    'images'      => [
                        $product->image,
                    ],
                    'amount'      => 100 * $product->price,
                    'currency'    => 'usd',
                    'quantity'    => $item['quantity'],
                ];
            }

            $stripe = Stripe::make(env('STRIPE_SECRET'));
            $source = $stripe->checkout()->sessions()->create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'success_url'          => env('CHECKOUT_URL') . '/success?source={CHECKOUT_SESSION_ID}',
                'cancel_url'           => env('CHECKOUT_URL') . '/error',
            ]);

            $order->transaction_id = $source['id'];
            $order->save();

            \DB::commit();

            return $source;
        } catch (\Exception $e) {
            \DB::rollBack();

            return response([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function confirm(Request $request)
    {
        if (!$order = Order::where('transaction_id', $request->transaction_id)->first()) {
            return Response([
                'error' => 'Order not found',
            ], 404);
        }

        $order->completed = 1;
        $order->save();

        event(new OrderCompletedEvent($order));

        return [
            'message' => 'success',
        ];
    }
}
