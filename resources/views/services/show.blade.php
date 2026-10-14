@extends('layouts.app')

@section('content')
    <section class="page-hero section-dark"><div class="shell page-hero-inner"><div><div class="eyebrow">{{ $service->eyebrow }}</div><h1>{{ $service->name }}</h1><p>{{ $service->short_description }}</p><a class="button button-gold" href="{{ route('home') }}#contact" data-service-select="{{ $service->id }}">Request this service <span aria-hidden="true">↗</span></a></div><div class="page-hero-image"><img src="{{ $service->image_url }}" alt="{{ $service->image_alt }}"></div></div></section>
    <section class="section service-detail"><div class="shell narrow"><div class="eyebrow">What to expect</div><h2>Care built around your vehicle.</h2><p class="lead">{{ $service->description }}</p><div class="included-list"><h3>Typical inclusions</h3><div>@foreach ($service->includes ?? [] as $include)<span><b>✓</b>{{ $include }}</span>@endforeach</div></div><a class="text-link" href="{{ route('home') }}#services">← Back to services</a></div></section>
@endsection
