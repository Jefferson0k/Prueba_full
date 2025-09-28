<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view bookings');
    }

    public function view(User $user, Booking $booking)
    {
        return $user->hasPermissionTo('view bookings') ||
               $user->branches()->where('branches.id', $booking->room->floor->branch_id)->exists() ||
               $booking->created_by === $user->id;
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create bookings');
    }

    public function update(User $user, Booking $booking)
    {
        return $user->hasPermissionTo('update bookings') ||
               ($user->hasPermissionTo('update own bookings') && $booking->created_by === $user->id);
    }

    public function delete(User $user, Booking $booking)
    {
        return $user->hasPermissionTo('delete bookings');
    }

    public function cancel(User $user, Booking $booking)
    {
        return $user->hasPermissionTo('cancel bookings') ||
               ($user->hasPermissionTo('cancel own bookings') && $booking->created_by === $user->id);
    }

    public function checkIn(User $user, Booking $booking)
    {
        return $user->hasPermissionTo('checkin bookings') && 
               $booking->status === Booking::STATUS_CONFIRMED;
    }

    public function checkOut(User $user, Booking $booking)
    {
        return $user->hasPermissionTo('checkout bookings') && 
               $booking->status === Booking::STATUS_CHECKED_IN;
    }

    public function addConsumption(User $user, Booking $booking)
    {
        return $user->hasPermissionTo('add booking consumptions') &&
               $booking->status === Booking::STATUS_CHECKED_IN;
    }
}