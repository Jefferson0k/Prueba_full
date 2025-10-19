<?php

namespace App\Http\Controllers\Web\PagoPersonal;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
class PagoPersonalWeb extends Controller{
    public function view(): Response{
        return Inertia::render('panel/PagoPersonal/indexPagoPersonal');
    }
}
