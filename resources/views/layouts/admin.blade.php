```blade
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') | Purple Blend</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/purple-blend-admin.css') }}">
</head>
<body class="admin-body">

@if(session('admin_id'))
    <div class="admin-layout">

        <aside class="admin-sidebar" id="admin-sidebar">

            <a class="admin-sidebar-brand" href="{{ route('admin.dashboard') }}">
                <img
                    src="{{ asset('images/purple-blend-logo.jpg') }}"
                    alt="Purple Blend"
                >
            </a>

            <button
                class="admin-menu-toggle"
                type="button"
                id="admin-menu-toggle"
                aria-controls="admin-sidebar-navigation"
                aria-expanded="false"
            >
                <span aria-hidden="true">&#9776;</span>
                <span>Admin Menu</span>
            </button>

            <nav class="admin-sidebar-nav" id="admin-sidebar-navigation">
                <a href="{{ route('admin.dashboard') }}"
                   @class(['active' => request()->routeIs('admin.dashboard')])>
                    Home
                </a>

                <a href="{{ route('admin.products.index') }}"
                   @class(['active' => request()->routeIs('admin.products.*')])>
                    Menu
                </a>

                <a href="{{ route('admin.inventory.index') }}"
                   @class(['active' => request()->routeIs('admin.inventory.*')])>
                    Stock
                </a>

                <a href="{{ route('admin.orders.index') }}"
                   @class(['active' => request()->routeIs('admin.orders.*')])>
                    Orders
                </a>

                <a href="{{ route('admin.customers.index') }}"
                   @class(['active' => request()->routeIs('admin.customers.*')])>
                    Customers
                </a>

                <a href="{{ route('admin.sales') }}"
                   @class(['active' => request()->routeIs('admin.sales', 'admin.reports')])>
                    Sales
                </a>

                <a href="{{ url('/admin/account') }}"
                   @class(['active' => request()->is('admin/account')])>
                    Account
                </a>

                @if(session('admin_role') === 'manager')
                    <a href="{{ route('admin.accounts.index') }}"
                       @class(['active' => request()->routeIs('admin.accounts.*')])>
                        Administrator Accounts
                    </a>
                @endif

                <div class="admin-sidebar-bottom">
                    <div class="admin-sidebar-user">
                        <strong>{{ session('admin_username', 'Administrator') }}</strong>
                        <small>{{ ucfirst(session('admin_role', 'staff')) }}</small>
                    </div>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="admin-logout">
                            Log Out
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <main class="admin-main">

            @if(session('status'))
                <div class="notice">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="notice error">
                    <strong>Please check the form:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')

        </main>
    </div>
@else
    <main class="page admin-page">
        @if(session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="notice error">
                <strong>Please check the form:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
@endif

@vite('resources/js/app.js')

<script>
    (() => {
        const toggle = document.getElementById('admin-menu-toggle');
        const navigation = document.getElementById('admin-sidebar-navigation');

        if (!toggle || !navigation) {
            return;
        }

        toggle.addEventListener('click', () => {
            const isOpen = navigation.classList.toggle('is-open');

            toggle.setAttribute('aria-expanded', String(isOpen));
        });
    })();
</script>

</body>
</html>
```