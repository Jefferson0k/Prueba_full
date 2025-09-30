<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\Provider\ProviderResource;
use App\Models\Provider;
use App\Pipelines\Provider\ProviderPipeline;
use Illuminate\Http\Request;

class ProviderController extends Controller{
    public function index(Request $request){
        $perPage = $request->input('per_page', 15);
        $query = Provider::query();
        $query = app(ProviderPipeline::class)->handle($query);
        $providers = $query->paginate($perPage);
        return ProviderResource::collection($providers);
    }
}
