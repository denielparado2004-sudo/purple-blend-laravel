@extends('layouts.admin')
@section('title', 'Admin sign in')
@section('content')
<section class="auth-card"><a class="back-link" href="{{ route('home') }}">← Storefront</a><p class="eyebrow">PURPLE BLEND MANAGEMENT</p><h1>Admin sign in</h1>
    <form method="post" action="{{ route('admin.login.submit') }}" class="form-stack">@csrf
        <label>Username<input name="username" value="{{ old('username') }}" autocomplete="username" required></label>
        <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
        <button class="button">Sign in</button>
    </form>
</section>
@endsection
