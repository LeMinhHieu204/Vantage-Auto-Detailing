@extends('layouts.app')

@section('content')
    <section class="hero section-dark" id="home">
        <div class="hero-image" role="img" aria-label="A dark sports car inside a detailing studio"></div>
        <div class="hero-shade"></div>
        <div class="shell hero-content">
            <div class="eyebrow">Premium car care</div>
            <h1>{!! nl2br(e($settings['hero_heading'] ?? "A Cleaner Car.\nA Better Drive.")) !!}</h1>
            <p>{{ $settings['hero_description'] ?? 'Professional auto detailing and protection services to keep your vehicle looking its best, inside and out.' }}</p>
            <div class="button-row">
                <a class="button button-gold" href="#contact">{{ $settings['primary_cta'] ?? 'Book an Appointment' }} <span aria-hidden="true">↗</span></a>
                <a class="button button-outline" href="#services">View Our Services <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </section>

    <section class="benefits section-dark" aria-label="Why customers choose Vantage">
        <div class="shell benefits-grid">
            <div class="benefit"><span class="line-icon">✦</span><span>Professional<br>detailing team</span></div>
            <div class="benefit"><span class="line-icon">◇</span><span>Quality<br>products</span></div>
            <div class="benefit"><span class="line-icon">◈</span><span>Attention<br>to detail</span></div>
            <div class="benefit"><span class="line-icon">↗</span><span>Customer-focused<br>service</span></div>
        </div>
    </section>

    <section class="section section-split" id="about">
        <div class="shell split-grid">
            <div class="section-copy">
                <div class="eyebrow">About us</div>
                <h2>More Than a<br>Car Wash</h2>
                <p>{{ $settings['about_text'] ?? 'At Vantage Auto Detailing, we believe every vehicle deserves proper care. Our services focus on cleaning, protecting and maintaining cars with the attention to detail their owners expect.' }}</p>
                <p>{{ $settings['about_supporting'] ?? 'From routine exterior detailing to paint correction and ceramic coating, we help drivers protect their vehicles and enjoy a cleaner, more comfortable driving experience.' }}</p>
                <a class="button button-gold" href="#services">Learn More About Us <span aria-hidden="true">→</span></a>
            </div>
            <div class="image-frame image-tall">
                <img src="https://images.unsplash.com/photo-1607860108855-64acf2078ed9?auto=format&fit=crop&w=1200&q=85" alt="Detailer carefully cleaning a dark vehicle in a studio" loading="lazy">
            </div>
        </div>
    </section>

    <section class="section section-services" id="services">
        <div class="shell">
            <div class="section-heading centered">
                <div class="eyebrow">Our services</div>
                <h2>Complete Detailing Solutions</h2>
                <p>Explore professional car care services tailored to your vehicle's condition and your needs.</p>
            </div>
            <div class="service-grid">
                @foreach ($services as $service)
                    <article class="service-card">
                        <div class="card-image"><img src="{{ $service->image_url }}" alt="{{ $service->image_alt }}" loading="lazy"><span class="card-badge">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></div>
                        <div class="card-body">
                            <div class="card-kicker">{{ $service->eyebrow }}</div>
                            <h3>{{ $service->name }}</h3>
                            <p>{{ $service->short_description }}</p>
                            <div class="card-links"><a href="{{ route('services.show', $service->slug) }}">Learn more <span aria-hidden="true">→</span></a><a href="#contact" data-service-select="{{ $service->id }}">Book this service <span aria-hidden="true">↗</span></a></div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section-feature">
        <div class="shell feature-grid">
            <div class="feature-photo"><img src="https://images.unsplash.com/photo-1613214150384-8d33d4e8aef0?auto=format&fit=crop&w=1400&q=85" alt="Technician polishing a car panel" loading="lazy"></div>
            <div class="feature-copy">
                <div class="eyebrow">Why choose Vantage</div>
                <h2>Quality. Detail. Results.</h2>
                <p>We combine simple, premium products with a measured process built around your vehicle's condition. If a service needs a closer inspection, we'll tell you before we begin.</p>
                <div class="mini-stats"><span><strong>01</strong> Attention<br>to detail</span><span><strong>02</strong> Clear service<br>recommendations</span><span><strong>03</strong> Care that fits<br>your vehicle</span></div>
            </div>
        </div>
    </section>

    <section class="section section-packages" id="packages">
        <div class="shell">
            <div class="section-heading centered">
                <div class="eyebrow">Our packages</div>
                <h2>Detailing Packages</h2>
                <p>Straightforward options for different vehicles and detailing needs.</p>
            </div>
            <div class="package-grid">
                @foreach ($packages as $package)
                    <article class="package-card {{ $package->is_featured ? 'is-featured' : '' }}">
                        @if ($package->is_featured)<div class="featured-label">Most popular</div>@endif
                        <div class="package-top"><h3>{{ $package->name }}</h3><p>{{ $package->tagline }}</p></div>
                        <div class="package-price"><small>{{ $package->price_label }}</small><strong>{{ $package->price }}</strong></div>
                        <ul>@foreach ($package->features ?? [] as $feature)<li><span aria-hidden="true">✓</span>{{ $feature }}</li>@endforeach</ul>
                        <a class="button {{ $package->is_featured ? 'button-gold' : 'button-outline' }}" href="#contact" data-package-select="{{ $package->id }}">Book now <span aria-hidden="true">↗</span></a>
                    </article>
                @endforeach
            </div>
            <p class="fine-print centered">Starting prices are illustrative and subject to vehicle size, condition and service scope. Ceramic coating and PPF are quoted separately after consultation.</p>
        </div>
    </section>

    <section class="section process-section">
        <div class="shell">
            <div class="section-heading centered"><div class="eyebrow">Our process</div><h2>How It Works</h2><p>A simple path from booking request to a beautifully cared-for car.</p></div>
            <div class="process-grid">
                @foreach ([['01', 'Choose your service', 'Select a service or package that fits your vehicle.'], ['02', 'Request an appointment', 'Choose a preferred date and share a little about your car.'], ['03', 'We detail your vehicle', 'Our team performs the agreed service with care.'], ['04', 'Enjoy the results', 'Collect your vehicle and receive maintenance guidance.']] as $step)
                    <div class="process-step"><span class="step-number">{{ $step[0] }}</span><h3>{{ $step[1] }}</h3><p>{{ $step[2] }}</p></div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section work-section" id="work">
        <div class="shell">
            <div class="section-heading centered"><div class="eyebrow">Our work</div><h2>Before &amp; After</h2><p>Explore the slider interaction below. This development demo uses sourced images and is not presented as verified customer results.</p></div>
            <div class="work-layout">
                <div class="comparison" data-comparison tabindex="0" aria-label="Demonstration before and after image comparison">
                    <img class="comparison-after" src="{{ $beforeAfter?->afterMedia?->url ?? 'https://images.unsplash.com/photo-1542282088-72c9c27ed0cd?auto=format&fit=crop&w=1600&q=86' }}" alt="{{ $beforeAfter ? $beforeAfter->afterMedia->alt_text : 'Demonstration after image of a glossy dark vehicle' }}">
                    <div class="comparison-before" data-before><img src="{{ $beforeAfter?->beforeMedia?->url ?? 'https://images.unsplash.com/photo-1607860108855-64acf2078ed9?auto=format&fit=crop&w=1600&q=86' }}" alt="{{ $beforeAfter ? $beforeAfter->beforeMedia->alt_text : 'Demonstration before image of a dark vehicle in a studio' }}"></div>
                    <span class="compare-label before-label">Before</span><span class="compare-label after-label">After</span><span class="compare-handle" data-handle><span>↔</span></span>
                    <input class="comparison-range" type="range" min="0" max="100" value="50" aria-label="Reveal more of the after image">
                </div>
                <div class="work-thumbnails">
                    @foreach ($gallery as $item)
                        <button type="button" class="work-thumb {{ $loop->first ? 'is-active' : '' }}" aria-label="{{ $item->title }}"><img src="{{ $item->image_url }}" alt="{{ $item->image_alt }}" loading="lazy"><span>{{ $item->category }}</span></button>
                    @endforeach
                </div>
            </div>
            <div class="demo-note"><span aria-hidden="true">i</span><span>@if($beforeAfter)<strong>Approved pair:</strong> {{ $beforeAfter->title }}. @else<strong>Development demo:</strong> before-and-after proof should only be published after an authentic same-vehicle, same-angle pair is uploaded and approved in Admin.@endif</span></div>
        </div>
    </section>

    <section class="section reviews-section" id="reviews">
        <div class="shell">
            <div class="section-heading centered"><div class="eyebrow">What customers say</div><h2>Customer Reviews</h2><p>Real feedback will be published here after review and approval.</p></div>
            <div class="reviews-grid">
                @forelse ($reviews as $review)
                    <article class="review-card {{ $review->is_placeholder ? 'is-placeholder' : '' }}">
                        <div class="stars" aria-label="{{ $review->rating ? $review->rating.' out of 5 stars' : 'Rating pending' }}">{{ $review->rating ? str_repeat('★', $review->rating) : '— — —' }}</div>
                        <blockquote>“{{ $review->review_text }}”</blockquote>
                        <div class="review-meta"><span class="avatar">{{ substr($review->customer_name, 0, 1) }}</span><span><strong>{{ $review->customer_name }}</strong><small>{{ $review->service_name ?? 'Customer' }}</small></span></div>
                    </article>
                @empty
                    <article class="review-card is-placeholder"><div class="stars">— — —</div><blockquote>Approved customer feedback will appear here.</blockquote><div class="review-meta"><span class="avatar">V</span><span><strong>Reviews pending</strong><small>Admin approval required</small></span></div></article>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section contact-section" id="contact">
        <div class="shell contact-grid">
            <div class="booking-panel">
                <div class="eyebrow">Get in touch</div>
                <h2>Book an Appointment</h2>
                <p>Tell us about your vehicle and the service you're interested in. We'll help arrange the next available appointment.</p>
                @if (session('booking_reference'))
                    <div class="success-message" role="status"><strong>Request received.</strong><br>Your reference is <b>{{ session('booking_reference') }}</b>. We’ll follow up to confirm timing and scope.</div>
                @endif
                @if ($errors->any())
                    <div class="error-message" role="alert">Please review the highlighted fields and try again.</div>
                @endif
                <form method="POST" action="{{ route('booking.store') }}" class="booking-form" novalidate>
                    @csrf
                    <div class="form-grid two-col">
                        <label>Full name <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="Your name" required>@error('full_name')<small>{{ $message }}</small>@enderror</label>
                        <label>Email address <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>@error('email')<small>{{ $message }}</small>@enderror</label>
                    </div>
                    <div class="form-grid two-col">
                        <label>Phone number <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="(555) 000-0000" required>@error('phone')<small>{{ $message }}</small>@enderror</label>
                        <label>Vehicle year <input type="text" name="vehicle_year" value="{{ old('vehicle_year') }}" placeholder="Optional"></label>
                    </div>
                    <div class="form-grid two-col">
                        <label>Vehicle make <input type="text" name="vehicle_make" value="{{ old('vehicle_make') }}" placeholder="e.g. Porsche" required>@error('vehicle_make')<small>{{ $message }}</small>@enderror</label>
                        <label>Vehicle model <input type="text" name="vehicle_model" value="{{ old('vehicle_model') }}" placeholder="e.g. 911 Carrera" required>@error('vehicle_model')<small>{{ $message }}</small>@enderror</label>
                    </div>
                    <div class="form-grid two-col">
                        <label>Service interested in <select name="service_id" data-service-field><option value="">Select a service</option>@foreach ($services as $service)<option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->name }}</option>@endforeach</select></label>
                        <label>Package <select name="package_id" data-package-field><option value="">No package selected</option>@foreach ($packages as $package)<option value="{{ $package->id }}" @selected(old('package_id') == $package->id)>{{ $package->name }} · {{ $package->price }}</option>@endforeach</select></label>
                    </div>
                    <div class="form-grid two-col">
                        <label>Preferred date <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" min="{{ now()->toDateString() }}" required>@error('preferred_date')<small>{{ $message }}</small>@enderror</label>
                        <label>Preferred time <select name="preferred_time"><option value="">Any available time</option><option>Morning</option><option>Afternoon</option></select></label>
                    </div>
                    <label>Additional notes <textarea name="notes" rows="3" placeholder="Tell us about the vehicle or any specific requests.">{{ old('notes') }}</textarea></label>
                    <label class="honeypot" aria-hidden="true">Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    <label class="checkbox-label"><input type="checkbox" name="privacy_consent" value="1" @checked(old('privacy_consent')) required><span>I agree to be contacted about this request and have read the privacy notice.</span></label>
                    <button class="button button-gold button-wide" type="submit">Send Booking Request <span aria-hidden="true">↗</span></button>
                    <p class="form-note">This sends a request, not an instant confirmation. Final timing and pricing are confirmed by the team.</p>
                </form>
            </div>
            <aside class="contact-card">
                <div class="contact-image"><img src="https://images.unsplash.com/photo-1563720223185-11003d516935?auto=format&fit=crop&w=1200&q=85" alt="Dark vehicle parked in a clean detailing space" loading="lazy"></div>
                <div class="contact-details">
                    <div class="detail-row"><span class="detail-icon">⌖</span><span><strong>Location</strong><small>{{ $settings['location_label'] ?? 'Location details available on request' }}</small></span></div>
                    <div class="detail-row"><span class="detail-icon">◷</span><span><strong>Business hours</strong><small>{!! nl2br(e($settings['hours'] ?? "Mon–Sat · 8:00 AM–6:00 PM\nSunday · Closed")) !!}</small></span></div>
                    <div class="detail-row"><span class="detail-icon">✉</span><span><strong>Email</strong><small>{{ $settings['contact_email'] ?? 'hello@example.com' }}</small></span></div>
                    <div class="detail-row"><span class="detail-icon">◌</span><span><strong>Phone</strong><small>{{ $settings['contact_phone'] ?? 'Add verified phone number' }}</small></span></div>
                </div>
            </aside>
        </div>
    </section>

    <footer class="site-footer">
        <div class="shell footer-top"><a class="brand" href="#home"><span>VANTAGE</span><small>AUTO DETAILING</small></a><div class="footer-links"><a href="#about">About</a><a href="#services">Services</a><a href="#work">Our Work</a><a href="#packages">Packages</a><a href="#contact">Contact</a></div><a class="button button-outline button-small" href="#contact">Book an Appointment <span aria-hidden="true">↗</span></a></div>
        <div class="shell footer-bottom"><span>© {{ now()->year }} Vantage Auto Detailing. All rights reserved.</span><span>{{ $settings['footer_note'] ?? 'Replace sample content before launch.' }}</span><span><a href="{{ route('privacy') }}">Privacy Policy</a><a href="{{ route('terms') }}">Terms of Service</a></span></div>
    </footer>
@endsection
