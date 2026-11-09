<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Admin;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_displays_available_menu_items(): void
    {
        MenuItem::create([
            'product_code' => 'PB-TEST-1',
            'name' => 'Purple Tea',
            'price' => 25,
            'category' => 'Drinks',
            'stock' => 5,
            'is_available' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Purple Tea')
            ->assertSee('Good mood.')
            ->assertSee('Find your favorites.')
            ->assertSee('All Items')
            ->assertSee('customer-site-header');
    }

    public function test_storefront_shows_all_menu_items_without_pagination_buttons(): void
    {
        for ($index = 1; $index <= 17; $index++) {
            MenuItem::create([
                'product_code' => "PB-PAGE-{$index}",
                'name' => "Menu Item {$index}",
                'price' => 25,
                'category' => 'Drinks',
                'stock' => 5,
                'is_available' => true,
            ]);
        }

        $this->get('/?page=2')
            ->assertOk()
            ->assertSee('Menu Item 1')
            ->assertSee('Menu Item 17')
            ->assertDontSee('pagination-nav')
            ->assertDontSee('Previous')
            ->assertDontSee('Next');
    }

    public function test_original_meryenda_filter_includes_legacy_dessert_category(): void
    {
        MenuItem::create([
            'product_code' => 'PB-TEST-4',
            'name' => 'Shanghai',
            'price' => 5,
            'category' => 'Meryenda / Dessert',
            'stock' => 4,
            'is_available' => true,
        ]);
        MenuItem::create([
            'product_code' => 'PB-TEST-5',
            'name' => 'Chicken Pastil',
            'price' => 40,
            'category' => 'Ulam',
            'stock' => 4,
            'is_available' => true,
        ]);

        $this->get('/?category=Meryenda')
            ->assertOk()
            ->assertSee('Shanghai')
            ->assertDontSee('Chicken Pastil');
    }

    public function test_product_image_uses_a_fallback_when_a_catalog_photo_is_missing(): void
    {
        $product = new MenuItem([
            'image_url' => 'images/missing-product-photo.jpg',
        ]);

        $this->assertNull($product->image_src);
    }

    public function test_customer_can_register_and_place_an_order_from_their_cart(): void
    {
        $product = MenuItem::create([
            'product_code' => 'PB-TEST-2',
            'name' => 'Milk Tea',
            'price' => 35,
            'category' => 'Drinks',
            'stock' => 4,
            'is_available' => true,
        ]);
        $secondProduct = MenuItem::create([
            'product_code' => 'PB-TEST-3',
            'name' => 'Purple Blend Snack',
            'price' => 10,
            'category' => 'Snacks',
            'stock' => 3,
            'is_available' => true,
        ]);

        $this->post('/register', [
            'first_name' => 'Taylor',
            'last_name' => 'Customer',
            'email' => 'taylor@example.test',
            'phone' => '09123456789',
            'password' => 'strong-password',
            'password_confirmation' => 'strong-password',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('customers', ['email' => 'taylor@example.test']);
        $this->assertTrue(Hash::check('strong-password', Customer::firstOrFail()->password_hash));

        $this->post('/cart/items', ['product_id' => $product->id, 'quantity' => 2])
            ->assertRedirect('/cart');
        $this->post('/cart/items', ['product_id' => $secondProduct->id, 'quantity' => 1])
            ->assertRedirect('/cart');

        $this->post('/checkout', [
            'customer_name' => 'Taylor Customer',
            'customer_email' => 'taylor@example.test',
            'customer_phone' => '09123456789',
            'dining_option' => 'takeaway',
            'pickup_date' => today()->toDateString(),
            'pickup_time' => '12:00',
            'notes' => '',
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'customer_id' => Customer::firstOrFail()->id,
            'total_amount' => 80,
            'order_status' => 'pending',
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_code' => 'PB-TEST-2',
            'quantity' => 2,
            'line_total' => 70,
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_code' => 'PB-TEST-3',
            'quantity' => 1,
            'line_total' => 10,
        ]);
        $this->assertSame(2, $product->fresh()->stock);
        $this->assertSame(2, $secondProduct->fresh()->stock);
        $this->assertSame([], session('cart', []));
        $order = Order::firstOrFail();
        $this->get('/orders')->assertOk()->assertSee($order->order_code);
        $this->get('/orders/'.$order->id)->assertOk()->assertSee($order->order_code);
        $this->get('/account')->assertOk()->assertSee('Account details');
    }

    public function test_admin_pages_require_an_active_admin_session(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_customer_can_reset_password_with_a_valid_one_time_token(): void
    {
        $customer = Customer::create([
            'first_name' => 'Taylor',
            'last_name' => 'Customer',
            'email' => 'taylor@example.test',
            'password_hash' => Hash::make('old-password'),
        ]);
        $token = Str::random(64);
        DB::table('customer_password_reset_tokens')->insert([
            'email' => $customer->email,
            'token' => hash('sha256', $token),
            'created_at' => now(),
        ]);

        $this->post('/reset-password', [
            'email' => $customer->email,
            'token' => $token,
            'password' => 'new-strong-password',
            'password_confirmation' => 'new-strong-password',
        ])->assertRedirect('/login');

        $this->assertTrue(Hash::check('new-strong-password', $customer->fresh()->password_hash));
        $this->assertDatabaseMissing('customer_password_reset_tokens', ['email' => $customer->email]);
    }

    public function test_forgot_password_sends_a_generic_confirmation_and_stores_a_hashed_token(): void
    {
        Customer::create([
            'first_name' => 'Taylor',
            'last_name' => 'Customer',
            'email' => 'taylor@example.test',
            'password_hash' => Hash::make('old-password'),
        ]);

        $this->post('/forgot-password', ['email' => 'taylor@example.test'])
            ->assertRedirect()
            ->assertSessionHas('status', 'If an account exists for that email, a password reset link has been sent.');

        $reset = DB::table('customer_password_reset_tokens')->where('email', 'taylor@example.test')->first();
        $this->assertNotNull($reset);
        $this->assertSame(64, strlen($reset->token));
    }

    public function test_admin_can_sign_in_and_open_the_dashboard(): void
    {
        Admin::create([
            'username' => 'store-manager',
            'first_name' => 'Store',
            'last_name' => 'Manager',
            'password_hash' => Hash::make('manager-password'),
            'role' => 'manager',
            'is_active' => true,
        ]);

        $this->post('/admin/login', [
            'username' => 'store-manager',
            'password' => 'manager-password',
        ])->assertRedirect('/admin');

        $this->get('/admin')->assertOk()->assertSee('Dashboard');
        $this->get('/admin/products')->assertOk()->assertSee('Products');
        $this->get('/admin/orders')->assertOk()->assertSee('Orders');
        $this->get('/admin/customers')->assertOk()->assertSee('Customers');
        $this->get('/admin/sales')->assertOk()->assertSee('last 30 days');
        $this->get('/admin/accounts')->assertOk()->assertSee('Administrator accounts');
    }
}
