@extends('layouts.app')

@section('content')
    <section class="auth-shell"><div class="auth-card"><a class="brand" href="{{ route('home') }}"><span>VANTAGE</span><small>AUTO DETAILING</small></a><div class="eyebrow">Admin access</div><h1>Sign in to manage the studio.</h1><p>Use the secure administrator account created with <code>php artisan vantage:admin</code>.</p>@if(session('error'))<div class="error-message">{{ session('error') }}</div>@endif<form method="POST" action="{{ route('admin.login.store') }}">@csrf<label>Email address<input type="email" name="email" value="{{ old('email') }}" required autofocus></label><label>Password<input type="password" name="password" required></label><label class="checkbox-label"><input type="checkbox" name="remember" value="1"><span>Remember this device</span></label><button class="button button-gold button-wide" type="submit">Sign in <span aria-hidden="true">↗</span></button></form></div></section>
@endsection
