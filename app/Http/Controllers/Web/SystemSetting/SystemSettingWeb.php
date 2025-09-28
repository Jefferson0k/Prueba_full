<?php

namespace App\Http\Controllers\Web\SystemSetting;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SystemSettingWeb extends Controller{
    public function view(): Response{
        Gate::authorize('viewAny', arguments: SystemSetting::class);
        return Inertia::render('panel/SystemSetting/indexSystemSetting');
    }
}
