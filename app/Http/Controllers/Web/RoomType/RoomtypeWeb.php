<?php

namespace App\Http\Controllers\Web\RoomType;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
class RoomtypeWeb extends Controller{
    public function view(): Response{
        Gate::authorize('viewAny', RoomType::class);
        return Inertia::render('panel/RoomType/indexRoomType');
    }
}
