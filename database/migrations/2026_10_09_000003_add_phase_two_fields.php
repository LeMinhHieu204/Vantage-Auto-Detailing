<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedInteger('starting_price_cents')->nullable()->after('description');
            $table->char('currency', 3)->default('USD')->after('starting_price_cents');
            $table->unsignedInteger('duration_minutes')->nullable()->after('currency');
            $table->boolean('consultation_required')->default(false)->after('duration_minutes');
            $table->string('meta_title')->nullable()->after('consultation_required');
            $table->string('meta_description')->nullable()->after('meta_title');
            $table->timestamp('archived_at')->nullable()->after('is_active');
            $table->foreignId('media_id')->nullable()->after('image_alt')->constrained('media')->nullOnDelete();
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->text('description')->nullable()->after('tagline');
            $table->char('currency', 3)->default('USD')->after('price_label');
            $table->unsignedInteger('duration_minutes')->nullable()->after('currency');
            $table->timestamp('archived_at')->nullable()->after('is_active');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
            $table->date('appointment_date')->nullable()->after('preferred_time');
            $table->string('appointment_time')->nullable()->after('appointment_date');
            $table->text('internal_notes')->nullable()->after('notes');
            $table->text('reschedule_reason')->nullable()->after('internal_notes');
            $table->index(['appointment_date', 'appointment_time', 'status']);
        });

        Schema::table('gallery_items', function (Blueprint $table) {
            $table->foreignId('media_id')->nullable()->after('id')->constrained('media')->nullOnDelete();
            $table->string('license_note')->nullable()->after('source_note');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->string('source')->nullable()->after('service_name');
            $table->unsignedInteger('sort_order')->default(0)->after('is_published');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('type')->default('text')->after('key');
            $table->boolean('is_public')->default(true)->after('value');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['type', 'is_public']);
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['source', 'sort_order']);
        });
        Schema::table('gallery_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('media_id');
            $table->dropColumn('license_note');
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
            $table->dropConstrainedForeignId('vehicle_id');
            $table->dropColumn(['appointment_date', 'appointment_time', 'internal_notes', 'reschedule_reason']);
        });
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['description', 'currency', 'duration_minutes', 'archived_at']);
        });
        Schema::table('services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('media_id');
            $table->dropColumn(['starting_price_cents', 'currency', 'duration_minutes', 'consultation_required', 'meta_title', 'meta_description', 'archived_at']);
        });
    }
};
