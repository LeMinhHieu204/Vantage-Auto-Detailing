<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Vantage Auto Detailing' }}</title>
    <meta name="description" content="Professional auto detailing, paint correction and vehicle protection services from Vantage Auto Detailing.">
    <meta property="og:title" content="{{ $title ?? 'Vantage Auto Detailing' }}">
    <meta property="og:description" content="A cleaner car. A better drive.">
    <meta property="og:type" content="website">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header" data-header>
        <div class="shell header-inner">
            <a class="brand" href="{{ route('home') }}" aria-label="Vantage Auto Detailing home">
                <span>VANTAGE</span>
                <small>AUTO DETAILING</small>
            </a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" data-menu-toggle>
                <span></span><span></span><span></span><span class="sr-only">Open navigation</span>
            </button>
            <nav class="primary-nav" id="primary-navigation" aria-label="Primary navigation" data-navigation>
                <a href="{{ route('home') }}#home">Home</a>
                <a href="{{ route('home') }}#about">About</a>
                <a href="{{ route('home') }}#services">Services</a>
                <a href="{{ route('home') }}#work">Our Work</a>
                <a href="{{ route('home') }}#packages">Packages</a>
                <a href="{{ route('home') }}#reviews">Reviews</a>
                <a href="{{ route('home') }}#contact">Contact</a>
            </nav>
            <a class="button button-small button-gold header-cta" href="{{ route('home') }}#contact">Book an Appointment <span aria-hidden="true">↗</span></a>
        </div>
    </header>

    <main id="main-content">
        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
