@extends('layouts.store')
@section('title', 'Forgot password')
@section('content')
<section class="auth-card"><a class="auth-brand" href="{{ route('home') }}"><img src="{{ asset('images/purple-blend-logo.jpg') }}" alt="The Purple Blend"></a><p class="eyebrow">ACCOUNT RECOVERY</p><h1>Forgot your password?</h1><p class="muted">Enter the email address linked to your account. If it exists, we'll send you a reset link.</p>
    <form method="post" action="{{ route('password.email') }}" class="form-stack">@csrf
        <label>Email<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
        <button class="button">Send reset link</button>
    </form><p class="form-foot"><a href="{{ route('login') }}">Back to sign in</a></p>
</section>
@endsection
