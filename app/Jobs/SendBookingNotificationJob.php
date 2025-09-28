<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Notifications\BookingCreatedNotification;
use App\Notifications\BookingStatusChangedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBookingNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $booking;
    protected $type;

    public function __construct(Booking $booking, $type)
    {
        $this->booking = $booking;
        $this->type = $type;
    }

    public function handle()
    {
        switch ($this->type) {
            case 'created':
                $this->booking->client->notify(new BookingCreatedNotification($this->booking));
                break;
            case 'status_changed':
                $this->booking->client->notify(new BookingStatusChangedNotification($this->booking));
                break;
        }

        // También notificar al personal del hotel
        $users = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'reception');
        })->get();

        foreach ($users as $user) {
            $user->notify(new BookingStatusChangedNotification($this->booking));
        }
    }
}
