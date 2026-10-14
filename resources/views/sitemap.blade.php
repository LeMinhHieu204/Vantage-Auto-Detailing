<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ url('/') }}</loc></url>
    <url><loc>{{ url('/gallery') }}</loc></url>
    <url><loc>{{ url('/privacy-policy') }}</loc></url>
    <url><loc>{{ url('/terms-of-service') }}</loc></url>
    @foreach(\App\Models\Service::where('is_active', true)->get() as $service)<url><loc>{{ route('services.show', $service->slug) }}</loc></url>@endforeach
</urlset>
