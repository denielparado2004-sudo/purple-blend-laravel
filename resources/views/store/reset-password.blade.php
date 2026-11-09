@extends('layouts.store')
@section('title', 'Reset password')
@section('content')
<section class="auth-card"><a class="auth-brand" href="{{ route('home') }}"><img src="{{ asset('images/purple-blend-logo.jpg') }}" alt="The Purple Blend"></a><p class="eyebrow">ACCOUNT RECOVERY</p><h1>Choose a new password</h1>
    <form method="post" action="{{ route('password.update') }}" class="form-stack">@csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label>Email<input type="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" required></label>
        <div class="form-row"><label>New password<input type="password" name="password" autocomplete="new-password" minlength="8" required></label><label>Confirm password<input type="password" name="password_confirmation" autocomplete="new-password" required></label></div>
        <button class="button">Reset password</button>
    </form>
</section>
@endsection
