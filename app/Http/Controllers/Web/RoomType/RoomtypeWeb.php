<?php

namespace App\Http\Controllers\Web\RoomType;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
class RoomtypeWeb extends Controller{
    public function view(): Response{
        return Inertia::render('panel/RoomType/indexRoomType');
    }
}
