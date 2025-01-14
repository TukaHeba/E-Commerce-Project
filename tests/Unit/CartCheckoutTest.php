<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Hash;
use App\Models\Cart\Cart;
use App\Models\Product\Product;
use App\Models\User\User;
use App\Services\Cart\CartService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartCheckoutTest extends TestCase
{
    /**
     * Helper method to create a product with a random name.
     * @return Product|\Illuminate\Database\Eloquent\Model
     */
    private function createProduct1(){
        return Product::create([
            'name' => Str::random(10),
            'description' => 'A powerful device to keep you connected and entertained on the go. Features a high-resolution camera, fast processor, and long battery life.',
            'price' => 299.99,
            'product_quantity' => 22,
            'maincategory_subcategory_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    /**
     * Helper method to create a product with a random name.
     * @return Product|\Illuminate\Database\Eloquent\Model
     */
    private function createProduct2(){
        return Product::create([
            'name' => Str::random(10),
            'description' => 'Perfect for working and learning from anywhere. Lightweight and powerful.',
            'price' => 999.99,
            'product_quantity' => 15,
            'maincategory_subcategory_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    /**
     * Helper method to create a test user with dummy data.
     * @return User|\Illuminate\Database\Eloquent\Model
     */
    private function createTestUser(){
        return User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => Str::random(10) . '@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Test12345'),
            'phone' => '09999857854',
            'address' => 'Test Address',
            'is_male' => true,
            'birthdate' => '2000-01-01',
        ]);
    }
    /**
     * Test the checkout functionality to ensure that the correct calculation is performed.
     * This test ensures that the checkout process correctly calculates
     * @return void
     */
    public function test_checkout(){
        $customer = $this->createTestUser();

        // Create a cart for the user
        $cart = Cart::create([
            'user_id'=>$customer->id
        ]);

        // Create products to add to the cart
        $product1 = $this->createProduct1();
        $product2 = $this->createProduct2();

        // Add items to the cart
        $cart->cartItems()->create([
            'cart_id'=>$cart->id,
            'product_id'=>$product1->id,
            'quantity'=>2
        ]);
        $cart->cartItems()->create([
            'cart_id'=>$cart->id,
            'product_id'=>$product2->id,
            'quantity'=>1
        ]);
        Auth::login($customer);
        $checkoutResult = (new CartService())->checkout();

        //Validate the structure and contents of the checkout result
        $this->assertIsArray($checkoutResult);
        $this->assertArrayHasKey('cart_items', $checkoutResult);
        $this->assertArrayHasKey('total_price', $checkoutResult);

        // Validate the cart items
        $this->assertCount(2, $checkoutResult['cart_items']);
        $this->assertEquals(599.98, $checkoutResult['cart_items'][0]['total']); // itemTotal for product1
        $this->assertEquals(999.99, $checkoutResult['cart_items'][1]['total']); // itemTotal for product2

        $this->assertEquals(1599.97, $checkoutResult['total_price']); // Total of all items
    }
}
