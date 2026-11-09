<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function login(): View|RedirectResponse
    {
        if (Admin::whereKey(session('admin_id'))->where('is_active', true)->exists()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ]);
        $admin = Admin::where('username', $credentials['username'])
            ->where('is_active', true)
            ->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password_hash)) {
            return back()->withErrors(['username' => 'The username or password is incorrect.'])->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->session()->put([
            'admin_id' => $admin->id,
            'admin_username' => $admin->username,
            'admin_role' => $admin->role,
        ]);

        return redirect()->intended(route('admin.dashboard'))->with('status', 'Welcome to the admin dashboard.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'You have signed out.');
    }

    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'ordersToday' => Order::whereDate('created_at', today())->count(),
            'pendingOrders' => Order::whereIn('order_status', ['pending', 'confirmed', 'preparing', 'ready'])->count(),
            'customersCount' => Customer::count(),
            'productsCount' => MenuItem::count(),
            'salesToday' => Order::whereDate('created_at', today())
                ->whereNotIn('order_status', ['cancelled'])
                ->sum('total_amount'),
            'recentOrders' => Order::latest()->limit(8)->get(),
        ]);
    }

    public function products(Request $request): View
    {
        $products = MenuItem::query()
            ->when($request->filled('search'), fn ($query) => $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('product_code', 'like', '%'.$request->input('search').'%');
            }))
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.products', compact('products'));
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $data = $this->validatedProduct($request);
        $data['image_url'] = $request->file('image')?->storePublicly('products', 'public');
        $data['is_available'] = $request->boolean('is_available');
        MenuItem::create($data);

        return back()->with('status', 'Product added.');
    }

    public function updateProduct(Request $request, MenuItem $product): RedirectResponse
    {
        $data = $this->validatedProduct($request, $product);
        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->storePublicly('products', 'public');
        }
        $data['is_available'] = $request->boolean('is_available');
        $product->update($data);

        return back()->with('status', 'Product updated.');
    }

    public function deleteProduct(MenuItem $product): RedirectResponse
    {
        $product->delete();

        return back()->with('status', 'Product deleted.');
    }

    public function orders(Request $request): View
    {
        $orders = Order::query()
            ->when($request->filled('status'), fn ($query) => $query->where('order_status', (string) $request->input('status')))
            ->with('payment')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders', compact('orders'));
    }

    public function updateOrder(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'order_status' => ['nullable', Rule::in(['pending', 'preparing', 'completed', 'cancelled'])],
            'payment_action' => ['nullable', Rule::in(['mark_paid'])],
        ]);
        if (!isset($data['order_status']) && !isset($data['payment_action'])) {
            return back()->withErrors(['order' => 'Choose an order or payment action.']);
        }

        DB::transaction(function () use ($data, $order) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if (isset($data['order_status']) && $data['order_status'] !== $lockedOrder->order_status) {
                $transitions = [
                    'pending' => ['preparing', 'cancelled'],
                    'preparing' => ['completed', 'cancelled'],
                ];
                abort_unless(in_array($data['order_status'], $transitions[$lockedOrder->order_status] ?? [], true), 422);

                if ($data['order_status'] === 'cancelled') {
                    foreach ($lockedOrder->items()->whereNotNull('menu_item_id')->get() as $item) {
                        MenuItem::whereKey($item->menu_item_id)->increment('stock', $item->quantity);
                    }
                }
                $lockedOrder->update(['order_status' => $data['order_status']]);
            }

            if (($data['payment_action'] ?? null) === 'mark_paid') {
                $lockedOrder->payment()->updateOrCreate(
                    [],
                    ['payment_status' => 'paid', 'amount' => $lockedOrder->total_amount, 'paid_at' => now()]
                );
            }
        });

        return back()->with('status', 'Order updated.');
    }

    public function customers(): View
    {
        return view('admin.customers', ['customers' => Customer::withCount('orders')->latest()->paginate(25)]);
    }

    public function sales(): View
    {
        $sales = Order::query()
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->where('order_status', '!=', 'cancelled')
            ->get(['created_at', 'total_amount'])
            ->groupBy(fn (Order $order) => $order->created_at->toDateString())
            ->map(fn ($orders) => [
                'count' => $orders->count(),
                'total' => $orders->sum('total_amount'),
            ]);

        return view('admin.sales', [
            'sales' => collect(range(0, 29))
                ->map(function (int $daysAgo) use ($sales) {
                    $date = now()->subDays($daysAgo)->toDateString();

                    return [
                        'date' => Carbon::parse($date),
                        'count' => $sales->get($date, ['count' => 0, 'total' => 0])['count'],
                        'total' => $sales->get($date, ['count' => 0, 'total' => 0])['total'],
                    ];
                })
                ->reverse()
                ->values(),
        ]);
    }

    public function accounts(Request $request): View
    {
        $this->ensureManager($request);

        return view('admin.accounts', ['admins' => Admin::orderBy('username')->get()]);
    }

    public function storeAdmin(Request $request): RedirectResponse
    {
        $this->ensureManager($request);
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:admins,username'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'role' => ['required', Rule::in(['manager', 'staff'])],
            'password' => ['required', 'string', 'min:8'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['password_hash'] = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active');
        unset($data['password']);
        Admin::create($data);

        return back()->with('status', 'Administrator account created.');
    }

    public function updateAdmin(Request $request, Admin $admin): RedirectResponse
    {
        $this->ensureManager($request);
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('admins')->ignore($admin->id)],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'role' => ['required', Rule::in(['manager', 'staff'])],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($admin->id === $request->attributes->get('admin')->id && !$request->boolean('is_active')) {
            return back()->withErrors(['is_active' => 'You cannot deactivate your own account.']);
        }

        if ($data['password'] ?? null) {
            $data['password_hash'] = Hash::make($data['password']);
        }
        unset($data['password']);
        $data['is_active'] = $request->boolean('is_active');
        $admin->update($data);

        return back()->with('status', 'Administrator account updated.');
    }

    private function validatedProduct(Request $request, ?MenuItem $product = null): array
    {
        return $request->validate([
            'product_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('menu_items')->ignore($product?->id),
            ],
            'name' => ['required', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'category' => ['required', 'string', 'max:100'],
            'variant_group' => ['nullable', 'string', 'max:32'],
            'size_label' => ['nullable', 'string', 'max:20'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_available' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }

    private function ensureManager(Request $request): void
    {
        abort_unless($request->attributes->get('admin')->role === 'manager', 403);
    }
}
