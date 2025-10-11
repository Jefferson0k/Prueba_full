<?php

namespace App\Http\Controllers\Web\Cash;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
class CashWeb extends Controller{
    public function view(): Response{
        Gate::authorize('viewAny', CashRegister::class);
        return Inertia::render('panel/Cash/indexCash');
    }
}
