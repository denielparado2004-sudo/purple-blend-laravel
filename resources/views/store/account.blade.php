
@extends('layouts.store')

@section('title', 'My Account')

@section('content')
<div class="account-page">

    <div class="account-heading">
        <p class="eyebrow">YOUR PURPLE BLEND PROFILE</p>
        <h1>My Account</h1>
        <p class="muted">Manage your personal information and account security.</p>
    </div>

    {{-- Personal Information Box --}}
    <section class="auth-card account-section">
        <h2>Personal Information</h2>
        <p class="muted">Keep your contact details up to date.</p>

        <form method="post" action="{{ route('account.update') }}" class="form-stack">
            @csrf
            @method('PATCH')

            <div class="form-row">
                <label>First name
                    <input type="text" name="first_name"
                        value="{{ old('first_name', $customer->first_name) }}"
                        maxlength="100" required>
                </label>

                <label>Last name
                    <input type="text" name="last_name"
                        value="{{ old('last_name', $customer->last_name) }}"
                        maxlength="100" required>
                </label>
            </div>

            <label>Email address
                <input type="email" name="email"
                    value="{{ old('email', $customer->email) }}"
                    autocomplete="email" required>
            </label>

            <label>Phone number
                <input type="text" name="phone"
                    value="{{ old('phone', $customer->phone) }}"
                    maxlength="20">
            </label>

            <label>Area
                <input type="text" name="area"
                    value="{{ old('area', $customer->area) }}">
            </label>

            <button type="submit" class="button">Save Changes</button>
        </form>
    </section>

    {{-- Change Password Box --}}
    <section class="auth-card account-section">
        <h2>Change Password</h2>
        <p class="muted">Update your password to keep your account secure.</p>

        <form method="post" action="{{ route('account.password') }}" class="form-stack">
            @csrf
            @method('PATCH')

            <label>Current password
                <input type="password" name="current_password"
                    autocomplete="current-password" required>
            </label>

            <div class="form-row">
                <label>New password
                    <input type="password" name="password"
                        autocomplete="new-password" minlength="8" required>
                </label>

                <label>Confirm new password
                    <input type="password" name="password_confirmation"
                        autocomplete="new-password" minlength="8" required>
                </label>
            </div>

            <button type="submit" class="button">Change Password</button>
        </form>
    </section>

    {{-- Sign Out Box --}}
    <section class="auth-card account-section">
        <h2>Sign Out</h2>
        <p class="muted">Sign out of your Purple Blend customer account.</p>

        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-button">Sign Out</button>
        </form>
    </section>

</div>
@endsection
