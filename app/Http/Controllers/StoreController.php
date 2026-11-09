<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function index(Request $request): View
    {
        $products = MenuItem::query()
            ->where('is_available', true)
            ->when($request->filled('category') && $request->input('category') !== 'All', function ($query) use ($request) {
                $category = (string) $request->input('category');

                if ($category === 'Meryenda') {
                    $query->whereIn('category', ['Meryenda', 'Meryenda / Dessert']);
                } elseif ($category === 'Beverage') {
                    $query->whereIn('category', ['Beverage', 'Drinks']);
                } else {
                    $query->where('category', $category);
                }
            })
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->input('search').'%'))
            ->orderByDesc('id')
            ->get();

        return view('store.index', compact('products'));
    }

    public function show(MenuItem $product): View
    {
        abort_unless($product->is_available, 404);

        $variants = $product->variant_group
            ? MenuItem::where('variant_group', $product->variant_group)->orderBy('price')->get()
            : collect();
        $relatedProducts = MenuItem::where('category', $product->category)
            ->whereKeyNot($product->id)
            ->where('is_available', true)
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return view('store.show', compact('product', 'variants', 'relatedProducts'));
    }

    public function cart(Request $request): View
    {
        return view('store.cart', ['items' => $this->cartProducts($request)]);
    }

    public function addToCart(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:menu_items,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ]);
        $product = MenuItem::findOrFail($data['product_id']);

        if (!$product->is_available || $product->stock < $data['quantity']) {
            return back()->withErrors(['quantity' => 'The requested quantity is not available.']);
        }

        $cart = $request->session()->get('cart', []);
        $quantity = (int) ($cart[$product->id] ?? 0) + $data['quantity'];
        if ($quantity > $product->stock) {
            return back()->withErrors(['quantity' => 'There is not enough stock for that quantity.']);
        }

        $cart[$product->id] = $quantity;
        $request->session()->put('cart', $cart);

        return redirect()->route('cart')->with('status', 'Added to your cart.');
    }

    public function updateCart(Request $request, MenuItem $product): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:50']]);
        if (!$product->is_available || $data['quantity'] > $product->stock) {
            return back()->withErrors(['quantity' => 'The requested quantity is not available.']);
        }

        $cart = $request->session()->get('cart', []);
        $cart[$product->id] = $data['quantity'];
        $request->session()->put('cart', $cart);

        return back()->with('status', 'Cart updated.');
    }

    public function removeFromCart(Request $request, MenuItem $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);

        return back()->with('status', 'Item removed from your cart.');
    }

    public function login(): View|RedirectResponse
    {
        if (Customer::find(session('customer_id'))) {
            return redirect()->route('home');
        }

        return view('store.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        $customer = Customer::where('email', $credentials['email'])->first();

        if (!$customer || !Hash::check($credentials['password'], $customer->password_hash)) {
            return back()->withErrors(['email' => 'The email or password is incorrect.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('customer_id', $customer->id);

        return redirect()->intended(route('home'))->with('status', 'Welcome back!');
    }

    public function register(): View|RedirectResponse
    {
        if (Customer::find(session('customer_id'))) {
            return redirect()->route('home');
        }

        return view('store.register');
    }

    public function storeCustomer(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $customer = Customer::create([
            ...$data,
            'password_hash' => Hash::make($data['password']),
        ]);

        $request->session()->regenerate();
        $request->session()->put('customer_id', $customer->id);

        return redirect()->route('home')->with('status', 'Your account has been created.');
    }

    public function forgotPassword(): View
    {
        return view('store.forgot-password');
    }

    public function emailPasswordReset(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:150']]);
        $customer = Customer::where('email', $data['email'])->first();

        if ($customer) {
            $token = Str::random(64);
            DB::table('customer_password_reset_tokens')->updateOrInsert(
                ['email' => $customer->email],
                ['token' => hash('sha256', $token), 'created_at' => now()]
            );
            $resetUrl = route('password.reset', ['token' => $token, 'email' => $customer->email]);
            Mail::raw(
                "Use this link within 60 minutes to reset your Purple Blend password:\n\n{$resetUrl}\n\nIf you did not request this, you can ignore this email.",
                fn ($message) => $message->to($customer->email)->subject('Reset your Purple Blend password')
            );
        }

        return back()->with('status', 'If an account exists for that email, a password reset link has been sent.');
    }

    public function resetPasswordForm(Request $request, string $token): View
    {
        return view('store.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:150'],
            'token' => ['required', 'string', 'size:64'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $reset = DB::table('customer_password_reset_tokens')->where('email', $data['email'])->first();

        if (!$reset || !hash_equals($reset->token, hash('sha256', $data['token'])) || now()->subMinutes(60)->gt($reset->created_at)) {
            return back()->withErrors(['email' => 'This password reset link is invalid or has expired.']);
        }

        $customer = Customer::where('email', $data['email'])->first();
        if (!$customer) {
            return back()->withErrors(['email' => 'This password reset link is invalid or has expired.']);
        }

        DB::transaction(function () use ($customer, $data) {
            $customer->update(['password_hash' => Hash::make($data['password'])]);
            DB::table('customer_password_reset_tokens')->where('email', $customer->email)->delete();
        });

        return redirect()->route('login')->with('status', 'Your password has been reset. You can now sign in.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'You have signed out.');
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $items = $this->cartProducts($request);
        if ($items->isEmpty()) {
            return redirect()->route('cart')->with('status', 'Your cart is empty.');
        }

        return view('store.checkout', [
            'items' => $items,
            'customer' => $request->attributes->get('customer'),
        ]);
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:201'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'dining_option' => ['required', Rule::in(['takeaway', 'dine_in'])],
            'pickup_date' => ['nullable', 'date', 'after_or_equal:today'],
            'pickup_time' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $cart = $request->session()->get('cart', []);
        if ($cart === []) {
            return redirect()->route('cart')->with('status', 'Your cart is empty.');
        }

        $customer = $request->attributes->get('customer');
        $order = DB::transaction(function () use ($cart, $data, $customer) {
            $products = MenuItem::query()
                ->whereKey(array_keys($cart))
                ->where('is_available', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($cart)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'cart' => 'A product in your cart is no longer available.',
                ]);
            }

            $subtotal = 0;
            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                if (!$product || $quantity < 1 || $quantity > $product->stock) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'cart' => 'A product in your cart no longer has enough stock.',
                    ]);
                }
                $subtotal += (float) $product->price * $quantity;
            }

            $order = Order::create([
                'order_code' => 'PB-'.now()->format('ymd').'-'.Str::upper(Str::random(6)),
                'customer_id' => $customer->id,
                'order_type' => 'online',
                'dining_option' => $data['dining_option'],
                'order_status' => 'pending',
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'pickup_date' => $data['pickup_date'] ?? null,
                'pickup_time' => $data['pickup_time'] ?? null,
                'notes' => $data['notes'] ?? null,
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
            ]);

            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                $product->decrement('stock', $quantity);
                $order->items()->create([
                    'menu_item_id' => $product->id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'line_total' => (float) $product->price * $quantity,
                ]);
            }

            return $order;
        });

        $request->session()->forget('cart');

        return redirect()->route('orders.show', $order)->with('status', 'Your order has been placed.');
    }

    public function orders(Request $request): View
    {
        $orders = $request->attributes->get('customer')->orders()->latest()->paginate(12);

        return view('store.orders', compact('orders'));
    }

    public function order(Request $request, Order $order): View
    {
        $this->ensureOrderOwner($request, $order);

        return view('store.order', ['order' => $order->load(['items', 'payment'])]);
    }

    public function cancelOrder(Request $request, Order $order): RedirectResponse
    {
        $this->ensureOrderOwner($request, $order);
        if ($order->order_status !== 'pending') {
            return back()->withErrors(['order' => 'Only pending orders can be cancelled.']);
        }

        DB::transaction(function () use ($order) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($order->order_status !== 'pending') {
                return;
            }
            $order->update(['order_status' => 'cancelled']);
            foreach ($order->items()->whereNotNull('menu_item_id')->get() as $item) {
                MenuItem::whereKey($item->menu_item_id)->increment('stock', $item->quantity);
            }
        });

        return back()->with('status', 'Order cancelled.');
    }

    public function account(Request $request): View
    {
        return view('store.account', ['customer' => $request->attributes->get('customer')]);
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $customer = $request->attributes->get('customer');
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('customers')->ignore($customer->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'area' => ['nullable', 'string', 'max:255'],
        ]);
        $customer->update($data);

        return back()->with('status', 'Your account has been updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $customer = $request->attributes->get('customer');

        if (!Hash::check($data['current_password'], $customer->password_hash)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        $customer->update(['password_hash' => Hash::make($data['password'])]);

        return back()->with('status', 'Your password has been changed.');
    }

    private function cartProducts(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        return MenuItem::query()
            ->whereKey(array_keys($cart))
            ->get()
            ->map(function (MenuItem $product) use ($cart) {
                $product->cart_quantity = (int) $cart[$product->id];
                $product->line_total = (float) $product->price * $product->cart_quantity;

                return $product;
            });
    }

    private function ensureOrderOwner(Request $request, Order $order): void
    {
        abort_unless($order->customer_id === $request->attributes->get('customer')->id, 404);
    }
}
