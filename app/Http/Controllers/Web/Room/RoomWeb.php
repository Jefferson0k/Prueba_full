<?php

namespace App\Http\Controllers\Web\Room;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class RoomWeb extends Controller{
    public function view($floorId){
        Gate::authorize('viewAny', Room::class);
        $floor = Floor::with([
            'subBranch.branch', 
            'rooms' => function ($query) {
                $query->with(['roomType', 'currentBooking'])->orderBy('room_number');
            }
        ])->findOrFail($floorId);
        return Inertia::render('panel/Room/indexRoom', [
            'floor' => $floor,
            'rooms' => $floor->rooms
        ]);
    }
}
