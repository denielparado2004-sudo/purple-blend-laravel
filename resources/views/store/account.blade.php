@extends('layouts.store')
@section('title', 'Account')
@section('content')
<p class="eyebrow">YOUR PROFILE</p><h1>Account details</h1>
<section class="auth-card"><form method="post" action="{{ route('account.update') }}" class="form-stack">@csrf @method('PATCH')
    <div class="form-row"><label>First name<input name="first_name" value="{{ old('first_name', $customer->first_name) }}" required></label><label>Last name<input name="last_name" value="{{ old('last_name', $customer->last_name) }}" required></label></div>
    <label>Email<input type="email" name="email" value="{{ old('email', $customer->email) }}" required></label>
    <label>Phone<input name="phone" value="{{ old('phone', $customer->phone) }}"></label>
    <label>Area<input name="area" value="{{ old('area', $customer->area) }}"></label>
    <button class="button">Save changes</button>
</form></section>
<section class="auth-card"><h2>Change password</h2><form method="post" action="{{ route('account.password') }}" class="form-stack">@csrf @method('PATCH')
    <label>Current password<input type="password" name="current_password" autocomplete="current-password" required></label>
    <div class="form-row"><label>New password<input type="password" name="password" autocomplete="new-password" minlength="8" required></label><label>Confirm new password<input type="password" name="password_confirmation" autocomplete="new-password" required></label></div>
    <button class="button">Change password</button>
</form></section>
<div class="form-foot"><form method="post" action="{{ route('logout') }}">@csrf<button class="text-button">Sign out</button></form></div>
@endsection
