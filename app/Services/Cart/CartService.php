<?php

namespace App\Services\Cart;

use Exception;
use App\Models\Cart\Cart;
use App\Models\Order\Order;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem\OrderItem;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendOrderConfirmationEmail;

class CartService
{
    /**
     * Checks out the cart by retrieving the cart items and calculating the total price.
     *
     * Retrieves the authenticated user's cart and its items.
     * Checks if the cart exists and if it contains items, throwing an exception if either condition is not met.
     * Uses the helper method to get and return the cart item details and the total price.
     *
     * @return array Contains the cart items data and the total price.
     */

    public function cartCheckout()
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->with('cartItems.product')->first();

        if (!$cart) {
            throw new Exception('The cart does not exist.');
        }

        if ($cart->cartItems->isEmpty()) {
            throw new Exception('The cart is empty.');
        }

        return $this->getCartItemsDataAndTotalPrice($cart->cartItems);
    }

    /**
     * Places an order by creating the order, saving order items, and clearing the cart.
     *
     * 1- Begins a database transaction to ensure data integrity.
     * 2- Fetch cart data using the cartCheckout method.
     * 3- Creates an order with the total price and shipping address.
     * 4- Saves the cart items as order items in the database.
     * 5- Fetch product and check its quantity, throwing exception if the quantity not enough
     * 6- Decrease product quantity
     * 7- Clears the user's cart after the order is placed.
     * 8- Commits the transaction if all steps are successful, or rolls back in case of an error.
     * 9- Dispatch the email notification job
     * 
     * @param string $shipping_address The address where the order will be shipped.
     * @return \App\Models\Order\Order The created order with its details.
     */
    public function placeOrder($shipping_address)
    {
        // Step 1
        DB::beginTransaction();

        try {
            // Step 2
            $cartData = $this->cartCheckout();

            // Step 3
            $order = Order::create([
                'user_id' => Auth::id(),
                'shipping_address' => $shipping_address,
                'total_price' => $cartData['total_price'],
                'status' => 'pending',
            ]);

            // Step 4
            foreach ($cartData['cart_items'] as $cartItemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItemData['product_id'],
                    'quantity' => $cartItemData['quantity'],
                    'price' => $cartItemData['price'],
                ]);

                // Step 5
                $product = Product::find($cartItemData['product_id']);
                if ($product->product_quantity < $cartItemData['quantity']) {
                    throw new Exception('Insufficient stock for product: ' . $product->name);
                }

                // Step 6
                $product->product_quantity -= $cartItemData['quantity'];
                $product->save();
            }

            // Step 7
            $cart = Cart::where('user_id', Auth::id())->first();
            $cart->cartItems()->delete();
            $cart->delete();

            // Step 8
            DB::commit();

            // Step 9
            $user = Auth::user();
            SendOrderConfirmationEmail::dispatch($user, $order);

            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Calculates the total price of all cart items and prepares the item data.
     *
     * 1- Loops through each cart item to calculate the item total price (quantity * price).
     * 2- Adds item data (id, name, quantity, price, total) to an array.
     * 3- Sums up the item totals to calculate the overall total price.
     * 4- Returns the item data and the final total price.
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems Collection of cart items.
     * @return array Contains 'cart_items' data and 'total_price'.
     */

    private function getCartItemsDataAndTotalPrice($cartItems)
    {
        $cartItemsData = [];
        $totalPrice = 0;

        foreach ($cartItems as $cartItem) {
            // Step 1
            $itemTotal = $cartItem->quantity * $cartItem->product->price;

            // Step 2
            $cartItemsData[] = [
                'product_id' => $cartItem->product_id,
                'product_name'  => $cartItem->product->name,
                'quantity'   => $cartItem->quantity,
                'price'      => $cartItem->product->price,
                'total'      => $itemTotal,
            ];

            // Step 3
            $totalPrice += $itemTotal;
        }

        // Step 4
        return ['cart_items'  => $cartItemsData, 'total_price' => $totalPrice];
    }
}
