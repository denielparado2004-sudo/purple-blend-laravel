@extends('layouts.store')
@section('title', 'Sign in')
@section('content')
<section class="auth-card"><a class="auth-brand" href="{{ route('home') }}"><img src="{{ asset('Images/purple-blend-logo.jpg') }}" alt="The Purple Blend"></a><p class="eyebrow">WELCOME BACK</p><h1>Sign in</h1><p class="muted">Sign in to place orders and follow their progress.</p>
    <form method="post" action="{{ route('login.submit') }}" class="form-stack">@csrf
        <label>Email<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
        <label>Password<input type="password" name="password" autocomplete="current-password" required><a class="field-link" href="{{ route('password.request') }}">Forgot your password?</a></label>
        <button class="button">Sign in</button>
    </form><p class="form-foot">New to Purple Blend? <a href="{{ route('register') }}">Create an account</a></p>
</section>
@endsection
