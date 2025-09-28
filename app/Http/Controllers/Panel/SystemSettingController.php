<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemSetting\SystemSettingResource;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Throwable;

class SystemSettingController extends Controller{
    public function index(Request $request){
        try {
            Gate::authorize('viewAny', SystemSetting::class);
            $group = $request->get('group', null);
            $settings = $group
                ? SystemSetting::byGroup($group)->active()->get()
                : SystemSetting::active()->get();
            return response()->json([
                'success' => true,
                'data' => SystemSettingResource::collection($settings)
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudieron obtener las configuraciones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, SystemSetting $setting){
        try {
            Gate::authorize('update', $setting);
            $request->validate([
                'value' => 'required'
            ]);
            $setting->value = $request->value;
            $setting->save();
            Cache::forget("setting.{$setting->key}");
            Cache::forget("settings.group.{$setting->group}");
            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada',
                'data' => new SystemSettingResource($setting)
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar la configuración.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show(string $key){
        try {
            Gate::authorize('viewAny', SystemSetting::class);
            $value = SystemSetting::get($key);
            return response()->json([
                'success' => true,
                'data' => [
                    'key' => $key,
                    'value' => $value
                ]
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo obtener la configuración.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
