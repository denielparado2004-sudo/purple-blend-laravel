@extends('layouts.admin')

@section('title', 'Account')

@section('content')

<p class="eyebrow">ADMINISTRATOR SETTINGS</p>
<h1>My Account</h1>

<p class="inventory-intro">
    Manage your administrator profile and account security.
</p>

<div class="admin-account-columns">

    <div>
        <section class="admin-account-card">
            <div class="admin-account-card-header">
                <h2>Personal Account</h2>
                <p>Update your administrator sign-in username.</p>
            </div>

            <div class="admin-account-card-body">
                <form method="POST" action="{{ route('admin.account.username') }}">
                    @csrf
                    @method('PATCH')

                    <label>
                        Username

                        <input
                            type="text"
                            name="username"
                            value="{{ old('username', $admin->username) }}"
                            maxlength="50"
                            required
                            autocomplete="username"
                        >
                    </label>

                    @error('username')
                        <small class="error">{{ $message }}</small>
                    @enderror

                    <button type="submit">Save Username</button>
                </form>
            </div>
        </section>

        <section class="admin-account-card">
            <div class="admin-account-card-header">
                <h2>Change Password</h2>
                <p>Use your current password to set a new one.</p>
            </div>

            <div class="admin-account-card-body">
                <form method="POST" action="{{ route('admin.account.password') }}">
                    @csrf
                    @method('PATCH')

                    <label>
                        Current Password
                        <input
                            type="password"
                            name="current_password"
                            required
                            autocomplete="current-password"
                        >
                    </label>

                    @error('current_password')
                        <small class="error">{{ $message }}</small>
                    @enderror

                    <label>
                        New Password
                        <input
                            type="password"
                            name="new_password"
                            minlength="8"
                            required
                            autocomplete="new-password"
                        >
                    </label>

                    <label>
                        Confirm New Password
                        <input
                            type="password"
                            name="new_password_confirmation"
                            minlength="8"
                            required
                            autocomplete="new-password"
                        >
                    </label>

                    @error('new_password')
                        <small class="error">{{ $message }}</small>
                    @enderror

                    <button type="submit">Change Password</button>
                </form>
            </div>
        </section>
    </div>

    <section class="admin-account-card">
        <div class="admin-account-card-header">
            <h2>Account Information</h2>
            <p>Your administrator account details.</p>
        </div>

        <div class="admin-account-card-body">
            <div class="admin-account-info">

                <div>
                    <span>Account Number</span>
                    <strong>#{{ $admin->id }}</strong>
                </div>

                <div>
                    <span>Account Type</span>
                    <strong>{{ ucfirst($admin->role) }}</strong>
                </div>

                <div>
                    <span>Email Address</span>
                    <strong>{{ $admin->email ?: 'Not provided' }}</strong>
                </div>

                <div>
                    <span>Account Status</span>
                    <strong>{{ $admin->is_active ? 'Active' : 'Inactive' }}</strong>
                </div>

                <div>
                    <span>First Name</span>
                    <strong>{{ $admin->first_name }}</strong>
                </div>

                <div>
                    <span>Last Name</span>
                    <strong>{{ $admin->last_name }}</strong>
                </div>

                <div>
                    <span>Account Created</span>
                    <strong>{{ $admin->created_at?->format('F j, Y') }}</strong>
                </div>

                <div>
                    <span>Last Updated</span>
                    <strong>{{ $admin->updated_at?->format('F j, Y') }}</strong>
                </div>

            </div>
        </div>
    </section>

</div>

@endsection