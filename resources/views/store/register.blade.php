
@extends('layouts.store')

@section('title', 'Create account')
@section('hide-navigation', true)

@section('content')
<section class="auth-card">
    <a class="auth-brand" href="{{ route('home') }}">
        <img src="{{ asset('images/purple-blend-logo.jpg') }}" alt="The Purple Blend">
    </a>

    <p class="eyebrow">JOIN THE NEIGHBORHOOD</p>
    <h1>Create your account</h1>

    <form method="post" action="{{ route('register.submit') }}" class="form-stack">
        @csrf

        <div class="form-row">
            <label>
                First name
                <input name="first_name" value="{{ old('first_name') }}" required maxlength="100">
            </label>

            <label>
                Last name
                <input name="last_name" value="{{ old('last_name') }}" required maxlength="100">
            </label>
        </div>

        <label>
            Email
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
        </label>

        <label>
            Phone <span class="muted">(optional)</span>
            <input name="phone" value="{{ old('phone') }}" maxlength="20">
        </label>

        <div class="form-row">
            <label>
                Password
                <input type="password" name="password" autocomplete="new-password" minlength="8" required>
            </label>

            <label>
                Confirm password
                <input type="password" name="password_confirmation" autocomplete="new-password" required>
            </label>
        </div>

        <button class="button">Create account</button>
    </form>

    <p class="form-foot">
        Already registered?
        <a href="{{ route('login') }}">Sign in</a>
    </p>
</section>
@endsection
