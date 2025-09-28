<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\Room\RoomTypeResource;
use App\Models\Room;
use App\Models\RoomType;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterByState;
use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pipeline\Pipeline;

class RoomTypeController extends Controller{
    use ApiResponse, AuthorizesRequests;
    public function __construct(){
        $this->authorizeResource(Room::class, 'room');
    }
    public function index(Request $request){
        //Gate::authorize('viewAny', RoomType::class);
        $perPage = $request->input('per_page', 15);
        $query = app(Pipeline::class)
            ->send(RoomType::query())
            ->through([
                new FilterByName($request->input('search')),
                new FilterByState($request->input('state')),
            ])
            ->thenReturn();
        return RoomTypeResource::collection($query->paginate($perPage));
    }
}