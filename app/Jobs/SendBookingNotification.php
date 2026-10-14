<?php

namespace App\Jobs;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBookingNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $bookingId, public string $event) {}

    public function handle(): void
    {
        $booking = Booking::with(['service', 'package'])->find($this->bookingId);
        if (! $booking) {
            return;
        }

        $recipient = $this->event === 'created' ? config('mail.from.address') : $booking->email;
        if (! $recipient) return;

        $subject = match ($this->event) {
            'created' => 'New Vantage booking request',
            'confirmed' => 'Vantage appointment confirmed',
            'cancelled' => 'Vantage appointment cancelled',
            'rescheduled' => 'Vantage appointment rescheduled',
            default => 'Vantage appointment update',
        };

        Mail::raw("Booking {$booking->reference} for {$booking->full_name}. Status: {$booking->status}. Appointment: ".($booking->appointment_date?->format('M j, Y').' '.$booking->appointment_time ?: 'To be confirmed').'.', function (Message $message) use ($recipient, $subject) {
            $message->to($recipient)->subject($subject);
        });
    }
}
