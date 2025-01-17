<?php

namespace App\Services\Cart;

use App\Models\Cart\Cart;
use App\Models\Order\Order;
use App\Models\Address\Country;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem\OrderItem;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendOrderConfirmationEmail;
use App\Services\Payment\PaymentService;

class CartService
{
    /**
     * Places an order by creating the order, saving order items, and clearing the cart.
     *
     * 1- Begin a database transaction to ensure data integrity.
     * 2- Fetch cart data using the checkout method.
     * 3- Validate the address and retrieve the zone ID.
     * 4- Create an order with the total price, postal code, and zone ID.
     * 5- Save the cart items as order items in the database.
     * 6- Fetch each product and reduce its quantity based on the order.
     * 7- Clear the user's cart after the order is placed.
     * 8- Commit the transaction if all steps are successful, or roll back in case of an error.
     * 9- Dispatch an email notification job to confirm the order.
     * 
     * @param array $data An array containing order details.
     * @return \App\Models\Order\Order The created order with its details.
     */
    public function placeOrder(array $data)
    {
        // Step 1
        DB::beginTransaction();

        try {
            // Step 2
            $cartData = $this->checkout();

            // Step 3
            $zoneId = $this->validateAddress($data['country_id'], $data['city_id'], $data['zone_id']);

            // Step 4
            $order = Order::create([
                'user_id' => Auth::id(),
                'zone_id' => $zoneId,
                'postal_code' => $data['postal_code'],
                'total_price' => $cartData['total_price'],
                'status' => 'pending',
            ]);

            // Step 5
            foreach ($cartData['cart_items'] as $cartItemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItemData['product_id'],
                    'quantity' => $cartItemData['quantity'],
                    'price' => $cartItemData['price'],
                ]);

                // Step 6
                $product = Product::find($cartItemData['product_id']);
                $product->product_quantity -= $cartItemData['quantity'];
                $product->save();
            }

            // Step 7
            $cart = Cart::where('user_id', Auth::id())->first();
            $cart->cartItems()->delete();

            // Step 8
            DB::commit();

            // Step 9
            $user = Auth::user();
            SendOrderConfirmationEmail::dispatch($user, $order);

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Checks out the cart by retrieving the cart items and calculating the total price.
     *
     * 1- Retrieves the authenticated user's cart and its items.
     * 2- Checks if the cart exists and if it contains items, throwing an exception if either condition is not met.
     * 3- Check product quantities.
     * 4- Uses the helper method to get and return the cart item details and the total price.
     *
     * @return array Contains the cart items data and the total price.
     */
    public function checkout()
    {
        // Step 1
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->with('cartItems.product')->first();

        // Step 2
        if (!$cart) {
            throw new \Exception('The cart does not exist.', 500);
        }

        if ($cart->cartItems->isEmpty()) {
            throw new \Exception('The cart is empty.', 500);
        }

        // Step 3
        foreach ($cart->cartItems as $cartItem) {
            $product = $cartItem->product;
            if ($product->product_quantity < $cartItem->quantity) {
                throw new \Exception('Insufficient stock for product: ' . $product->name);
            }
        }

        // Step 4
        return $this->getCartItemsDataAndTotalPrice($cart->cartItems);
    }

    /**
     * Calculates the total price of all cart items and prepares the item data.
     *
     * 1- Loop through each cart item to calculate the item's total price (quantity * price).
     * 2- Add item data (ID, name, quantity, price, total) to an array.
     * 3- Sum up the item totals to calculate the overall total price.
     * 4- Return the item data and the final total price.
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems A collection of cart items.
     * @return array An array containing 'cart_items' data and 'total_price'.
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

    /**
     * Validate the address and get the zone ID.
     * 
     * Checking the existence of the country then ensuring that city belongs to the specified country 
     * and the zone belongs to the specified city.
     * 
     * @param int $countryId The ID of the country.
     * @param int $cityId The ID of the city.
     * @param int $zoneId The ID of the zone.
     * @return int The ID of the validated zone.
     */
    public function validateAddress(int $countryId, int $cityId, int $zoneId): int
    {
        $country = Country::findOrFail($countryId);
        $city = $country->cities()->findOrFail($cityId);
        $zone = $city->zones()->findOrFail($zoneId);

        return $zone->id;
    }
}
