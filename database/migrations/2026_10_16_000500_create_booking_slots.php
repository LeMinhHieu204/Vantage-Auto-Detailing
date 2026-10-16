<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_slots', function (Blueprint $table): void {
            $table->id();
            $table->date('appointment_date');
            $table->string('appointment_time', 40);
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['appointment_date', 'appointment_time']);
        });

        DB::statement("INSERT INTO booking_slots (appointment_date, appointment_time, booking_id, created_at, updated_at)
            SELECT appointment_date, appointment_time, MIN(id), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            FROM bookings
            WHERE status = 'CONFIRMED' AND appointment_date IS NOT NULL AND appointment_time IS NOT NULL
            GROUP BY appointment_date, appointment_time");
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_slots');
    }
};
