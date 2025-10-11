<?php

namespace App\Http\Controllers\Web\Room;

use App\Http\Controllers\Controller;
use App\Http\Resources\Room\RoomResource;
use App\Models\Room;
use Inertia\Inertia;

class RoomFloorWeb extends Controller{
    public function view($id){
        $room = Room::with([
            'floor.subBranch.branch', 
            'roomType',
            'bookings',
            'statusLogs',
        ])->findOrFail($id);
        $roomData = new RoomResource($room);
        return Inertia::render('panel/RoomFloor/indexRoomFloor', [
            'data' => $roomData,
        ]);
    }
}
