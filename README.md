<a id="top"></a>

<div align="center">

# VANTAGE AUTO DETAILING

### Precision in Every Detail.

Premium automotive care, presented through a focused Laravel web experience for service discovery, booking requests and day-to-day detailing operations.

<p>
  <img src="https://img.shields.io/badge/PHP-8.3.16-777BB4?logo=php&logoColor=white" alt="PHP 8.3.16">
  <img src="https://img.shields.io/badge/Laravel-13.35.0-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13.35.0">
  <img src="https://img.shields.io/badge/Vite-8.3.4-646CFF?logo=vite&logoColor=white" alt="Vite 8.3.4">
  <img src="https://img.shields.io/badge/PHPUnit-12.5.38-3C9CD7?logo=php&logoColor=white" alt="PHPUnit 12.5.38">
  <img src="https://img.shields.io/badge/SQLite-3.40.0-003B57?logo=sqlite&logoColor=white" alt="SQLite 3.40.0">
</p>

<p>
  <strong>Laravel 13</strong> · <strong>Blade</strong> · <strong>Custom CSS</strong> · <strong>Vanilla JavaScript</strong>
</p>

</div>

> A dark, editorial automotive-detailing website with a real booking workflow and an authenticated operations area. The implementation is intentionally honest about demo imagery, pending reviews and production configuration.

No public repository URL or live demo URL was present in the audited workspace, so neither is invented here.

## About the project

Vantage Auto Detailing is a premium automotive-care website built around a simple customer journey: discover the right service, understand the scope, request an appointment and receive a follow-up from the detailing team.

The public experience is backed by Laravel and Eloquent rather than static marketing copy alone. Services, packages, gallery records, reviews and business settings are database-backed. Booking requests create a durable reference, customer and vehicle records, a starting status-history entry and a notification job for follow-up.

The visual direction is deliberately restrained: obsidian backgrounds, graphite panels, soft borders, ice-white type and a champagne-gold accent. The UI is authored in custom CSS with responsive breakpoints and small vanilla JavaScript interactions.

## Visual showcase

The source contains real Blade views and seeded development content for the following surfaces:

| Surface | Route | What is implemented |
| --- | --- | --- |
| Homepage | <code>/</code> | Hero, benefits, about, services, packages, process, work, reviews, booking and contact sections |
| Service detail | <code>/services/{slug}</code> | Database-backed service description, inclusions and booking handoff |
| Gallery | <code>/gallery</code> | Published gallery records with alt text and development-demo labeling |
| Booking confirmation | <code>/booking/{reference}</code> | Reference-based request summary and current status |
| Admin dashboard | <code>/admin</code> | Operational counts, recent requests and quick actions behind admin auth |
| Admin content tools | <code>/admin/services</code>, <code>/admin/packages</code>, <code>/admin/media</code>, <code>/admin/gallery</code>, <code>/admin/reviews</code> | Authenticated content management and moderation workflows |

No screenshot files are checked into this repository. Browser-control tooling was unavailable during the audit, so this README intentionally does not link to fabricated screenshots or AI-generated UI imagery.

## Core features

### Customer experience

- Service browsing with six seeded detailing services and published-only queries.
- Service detail pages with descriptions, typical inclusions and request handoff.
- Three seeded detailing packages with starting prices, feature lists and a featured package state.
- Booking request form covering customer details, vehicle details, service/package selection, preferred date/time, notes and privacy consent.
- Booking reference generation in the <code>VA-XXXXXXXX</code> format.
- Customer and vehicle records created or updated from a booking request.
- Booking confirmation page with status, vehicle, selected service/package and preferred date.
- Gallery page with source notes and explicit development-demo labels.
- Before-and-after comparison slider with range-input and keyboard interaction.
- Placeholder review state that keeps unapproved testimonials out of the public experience.

### Business operations

- Session-based administrator login with an <code>is_admin</code> authorization flag.
- Dashboard metrics for pending, confirmed, today, upcoming, completed, cancelled/no-show and new-enquiry states.
- Searchable and filterable booking list with status, service and date filters.
- Booking detail view with appointment assignment, internal notes and status history.
- Controlled booking status transitions and duplicate confirmed-slot protection.
- Service CRUD with pricing, duration, included features, SEO fields, visibility and archive behavior.
- Package CRUD with pricing, duration, features, featured state and service associations.
- Media library with validated image uploads, alt text, source/license notes and safe reference checks before deletion.
- Gallery uploads and authenticated before/after pair uploads, unpublished by default until review.
- Review creation, editing, moderation and deletion; placeholder reviews cannot be approved as testimonials.
- Business, hero, contact, booking-window and SEO settings managed from Admin.
- Customer records with vehicles and booking history.
- Audit log records for administrative mutations, including actor, action, IP address and user agent.

### Platform experience

- Responsive layouts for desktop, tablet and small-screen breakpoints.
- Mobile navigation drawer with active-section highlighting through <code>IntersectionObserver</code>.
- Service/package selection handoff to the booking form through optional <code>localStorage</code> state.
- Skip link, semantic labels, visible focus states, image alt text and reduced-motion support.
- CSRF protection, request validation, booking rate limiting and a honeypot field.
- Transactional booking writes with customer/vehicle upsert and status-history persistence.
- Queued booking notification job using the configured Laravel mail transport.

## Tech stack

| Layer | Verified technology | Purpose |
| --- | --- | --- |
| Frontend | Blade templates + vanilla JavaScript | Server-rendered public and Admin interfaces with small progressive enhancements |
| Backend | Laravel Framework 13.35.0 on PHP 8.3.16 | Routing, controllers, middleware, validation, sessions, jobs and mail integration |
| Data layer | Eloquent ORM + Laravel migrations | Models, relationships, casts, transactional writes and schema evolution |
| Database | SQLite 3.40.0 in the current local <code>.env</code>; MySQL-compatible configuration is documented | Local development persistence and isolated test support |
| Styling | Custom CSS in <code>resources/css/app.css</code> | Design tokens, dark theme, responsive layout, accessibility states and Admin styling |
| Build tooling | Vite 8.3.4 + <code>laravel-vite-plugin</code> 3.2.0 | Bundles CSS and JavaScript into <code>public/build</code> |
| Testing | PHPUnit 12.5.38 | Feature coverage for booking, authentication, admin operations, uploads, reviews and settings |
| Mail/queue | Laravel mail + queued <code>SendBookingNotification</code> job | Sends booking-created and appointment-status messages through configured mail settings |

Tailwind CSS packages are present in the frontend toolchain, but the current UI is authored in the custom stylesheet rather than Tailwind utility classes.

## Architecture

~~~mermaid
flowchart LR
    Visitor[Public visitor] --> Routes[Laravel web routes]
    Admin[Administrator] --> Auth[Session auth + is_admin middleware]
    Auth --> Routes
    Routes --> Controllers[Controllers + Form Requests]
    Controllers --> Views[Blade views]
    Controllers --> Models[Eloquent models]
    Models --> Database[(SQLite local runtime / configurable MySQL)]
    Controllers --> BookingFlow[Transactional booking workflow]
    BookingFlow --> Customer[Customer + vehicle records]
    BookingFlow --> History[Booking status history]
    BookingFlow --> Job[SendBookingNotification]
    Job --> Mail[Configured Laravel mail transport]
    Vite[Vite build] --> Assets[Compiled CSS + vanilla JavaScript]
    Assets --> Views
~~~

There is no separately deployed API, microservice boundary, payment gateway or frontend SPA in the verified source. Public and Admin routes share the Laravel application and Blade rendering pipeline.

### Booking lifecycle

1. The public form is validated by <code>StoreBookingRequest</code>, including date-window settings, privacy consent and the honeypot field.
2. <code>BookingController</code> applies a per-IP rate limit and opens a database transaction.
3. The customer and vehicle are created or updated, then the booking receives a unique reference and <code>PENDING</code> status.
4. An initial status-history record is saved with the booking.
5. A <code>SendBookingNotification</code> job is dispatched after the response for the configured mail transport.
6. Admin users can progress the booking through the controlled status map, assign a date/time, record notes and trigger status notifications.

## Project structure

~~~text
app/
├── Console/Commands/       # Administrator bootstrap command
├── Http/
│   ├── Controllers/        # Public and Admin request handlers
│   ├── Middleware/         # Admin authorization middleware
│   └── Requests/           # Booking Form Request validation
├── Jobs/                   # Queued booking notifications
├── Models/                 # Eloquent models and relationships
├── Providers/              # Application service providers
└── Services/               # Media upload and audit logging services
database/
├── migrations/             # Core and Phase 2 schema migrations
├── seeders/                # Development services, packages, gallery and settings
└── database.sqlite         # Current local SQLite database
resources/
├── css/app.css             # Custom design system and responsive styles
├── js/app.js               # Navigation, selection handoff and comparison slider
└── views/                  # Public, auth and Admin Blade templates
routes/
└── web.php                 # Public and authenticated Admin routes
tests/
├── Feature/                # Booking and Admin workflow coverage
└── Unit/                   # Unit test suite
public/build/               # Vite production output
docs/                       # Setup, asset and phase audit documentation
~~~

Generated dependencies, caches and <code>node_modules</code> are intentionally omitted from this overview.

## Getting started

### Prerequisites

- PHP 8.3 or newer with the required Laravel extensions.
- Composer 2.
- Node.js and npm; the local setup notes target Node.js 22.
- SQLite for the current local runtime, or MySQL 8+ when using the documented MySQL configuration.

### Install dependencies

From the application root:

~~~powershell
composer install
npm.cmd install
~~~

Create or configure a local, uncommitted <code>.env</code> for the machine where the app will run. The current workspace uses SQLite; do not copy credentials into the repository.

If the application key is not already configured:

~~~powershell
php artisan key:generate
~~~

### Prepare the database and storage

~~~powershell
php artisan migrate --seed
php artisan storage:link
~~~

The seed data is presentation-oriented development content: six services, three packages, four gallery demo items, one placeholder review and business settings with unverified contact details. It does not create an administrator account.

For MySQL values, storage/mail behavior and the non-destructive setup sequence, see [docs/LOCAL_SETUP.md](docs/LOCAL_SETUP.md).

### Create an administrator

~~~powershell
php artisan vantage:admin admin@example.com
~~~

The command prompts for the administrator name and password. Passwords are not seeded or documented.

### Build and run locally

~~~powershell
npm.cmd run build
php artisan serve --host=127.0.0.1 --port=8000
~~~

Open <code>http://127.0.0.1:8000</code>. The Admin login is at <code>/admin/login</code>.

### Mail and queue behavior

Local development defaults to Laravel's log mailer in the documented setup. Booking notifications are dispatched through the queue after a booking or status transaction; configure a real SMTP/API mailer and run a worker before relying on delivery:

~~~powershell
php artisan queue:work
~~~

Provider delivery has not been verified by this repository.

## Verification

The following checks were run against the current workspace:

- <code>php artisan migrate:status</code> — all six migrations are applied.
- <code>php artisan test --compact</code> — 11 tests and 59 assertions passed.
- <code>php artisan view:cache</code> — Blade templates cached successfully.
- <code>npm.cmd run build</code> — Vite production build completed successfully.
- <code>php artisan route:list --except-vendor</code> — public and Admin routes resolved.
- Local HTTP smoke checks — <code>/</code>, <code>/gallery</code>, <code>/services/exterior-detailing</code>, <code>/privacy-policy</code>, <code>/terms-of-service</code>, <code>/sitemap.xml</code> and <code>/admin/login</code> returned HTTP 200.

Browser viewport regression and screenshot capture were not run because the approved browser-control tool was unavailable in the audit environment.

## Security and quality notes

Implemented controls include CSRF-protected forms, session regeneration on Admin login, an <code>is_admin</code> middleware gate, hashed passwords, Form Request validation, rate limiting, a booking honeypot, database transactions, row locking for booking updates, confirmed-slot conflict checks, upload MIME/size/dimension validation, audit logging and safe media-reference checks.

The repository does not claim production readiness. Legal pages are starter copy, contact details are not verified, seeded Unsplash imagery is marked as development content, reviews remain pending until real feedback is supplied and approved, and mail-provider delivery still requires environment configuration.

## Project status and roadmap

### Completed

- [x] Premium dark-theme public website with database-backed services, packages and settings.
- [x] Service detail routes, package selection and booking-request workflow.
- [x] Customer/vehicle persistence, booking references and status history.
- [x] Authenticated Admin dashboard and operational booking management.
- [x] Service, package, media, gallery, before/after, review, settings, customer and audit-log modules.
- [x] Responsive CSS and vanilla JavaScript interactions in the source.
- [x] PHPUnit, Blade cache, Vite build, migration and local HTTP smoke verification.

### In progress / production prerequisites

- [ ] Replace development Unsplash hotlinks with owned or properly licensed production photography.
- [ ] Upload and approve authentic same-vehicle before/after pairs.
- [ ] Replace placeholder reviews and verify business contact information.
- [ ] Complete legal copy with appropriate business and legal review.
- [ ] Configure and verify a real mail provider, queue worker, HTTPS and operational backups.
- [ ] Run browser and viewport regression checks with an approved browser tool.

### Planned

No dated release plan or additional committed feature scope is present in the repository. The items above are production prerequisites, not promises of a release schedule.

## Documentation

- [VANTAGE_MASTER_REPORT.md](VANTAGE_MASTER_REPORT.md) — architecture, verification history and production blockers.
- [docs/LOCAL_SETUP.md](docs/LOCAL_SETUP.md) — local setup, MySQL, storage, mail, queue and test commands.
- [docs/IMAGE_ASSET_MANIFEST.md](docs/IMAGE_ASSET_MANIFEST.md) — seeded image sources and replacement notes.
- [docs/DESIGN_REFERENCE_ANALYSIS.md](docs/DESIGN_REFERENCE_ANALYSIS.md) — visual system and responsive layout decisions.

<div align="center">

**VANTAGE AUTO DETAILING**

*Precision. Protection. Perfection.*

[Back to top](#top)

</div>

