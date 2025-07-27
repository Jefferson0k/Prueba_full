<?php

use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\ConsultasDni;
use App\Http\Controllers\Api\HabitacionController;
use App\Http\Controllers\Api\HorarioController;
use App\Http\Controllers\Api\PisoController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\TipoHabitacionController;
use App\Http\Controllers\Api\UsoHabitacionController;
use App\Http\Controllers\Api\UsuariosController;
use App\Http\Controllers\Web\Categoria\CategoriaWeb;
use App\Http\Controllers\Web\Cliente\ClienteWeb;
use App\Http\Controllers\Web\Habitacion\HabitacionWeb;
use App\Http\Controllers\Web\Horario\HorarioWeb;
use App\Http\Controllers\Web\Piso\PisoWeb;
use App\Http\Controllers\Web\Producto\ProductoWeb;
use App\Http\Controllers\Web\TipoHabitacion\TipoHabitacionWeb;
use App\Http\Controllers\Web\UsoHabitacion\UsoHabitacionWeb;
use App\Http\Controllers\Web\UsuarioWebController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    #PARA QUE CUANDO SE CREA UN USUARIO O MODIFICA SU PASSWORD LO REDIRECCIONE PARA QUE PUEDA ACTUALIZAR
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return Inertia::render('Dashboard', [
            'mustReset' => $user->restablecimiento == 0,
        ]);
    })->name('dashboard');

    #VISTAS DEL FRONTEND
    Route::get('/usuario', [UsuarioWebController::class,'index'])->name('index.view');
    Route::get('/consulta/{dni}', [ConsultasDni::class, 'consultar'])->name('consultar.view');
    Route::get('/roles', [UsuarioWebController::class, 'roles'])->name('roles.view');
    Route::get('/clientes', [ClienteWeb::class, 'view'])->name('clientes.view');
    Route::get('/habitaciones', [HabitacionWeb::class, 'view'])->name('habitaciones.view');
    Route::get('/horarios', [HorarioWeb::class, 'view'])->name('horarios.view');
    Route::get('/pisos', [PisoWeb::class, 'view'])->name('pisos.view');
    Route::get('/tipos-habitaciones', [TipoHabitacionWeb::class, 'view'])->name('tipos-habitaciones.view');
    Route::get('/uso-habitaciones', [UsoHabitacionWeb::class, 'view'])->name('uso-habitaciones.view');
    Route::get('/categorias', [CategoriaWeb::class, 'view'])->name('categoria.view');
    Route::get('/productos', [ProductoWeb::class, 'view'])->name('categoria.view');

    #CLIENTES -> CLIENTES
    Route::prefix('cliente')->group(function () {
        Route::get('/', [ClienteController::class, 'index']);
        Route::get('/{cliente}', [ClienteController::class, 'show']);
        Route::post('/', [ClienteController::class, 'store']);
        Route::put('/{cliente}', [ClienteController::class, 'update']);
        Route::delete('/{cliente}', [ClienteController::class, 'destroy']);
    });

    #PRODUCTOS -> BACKEND
    Route::prefix('producto')->group(function(){
        Route::get('/', [ProductoController::class, 'index'])->name('Productos.index');
        Route::post('/',[ProductoController::class, 'store'])->name('Productos.store');
        Route::get('/{product}',[ProductoController::class, 'show'])->name('Productos.show');
        Route::put('/{product}',[ProductoController::class, 'update'])->name('Productos.update');
        Route::delete('/{product}',[ProductoController::class, 'destroy'])->name('Productos.destroy');
    });

    #HABITACIONES -> BACKEND
    Route::prefix('habitacion')->group(function () {
        Route::get('/', [HabitacionController::class, 'index']);
        Route::get('/{habitacion}', [HabitacionController::class, 'show']);
        Route::post('/', [HabitacionController::class, 'store']);
        Route::put('/{habitacion}', [HabitacionController::class, 'update']);
        Route::delete('/{habitacion}', [HabitacionController::class, 'destroy']);
    });

    #HORARIOS -> BACKEND
    Route::prefix('horario')->group(function () {
        Route::get('/', [HorarioController::class, 'index']);
        Route::get('/{horario}', [HorarioController::class, 'show']);
        Route::post('/', [HorarioController::class, 'store']);
        Route::put('/{horario}', [HorarioController::class, 'update']);
        Route::delete('/{horario}', [HorarioController::class, 'destroy']);
    });

    #=PISOS -> BACKEND
    Route::prefix('piso')->group(function () {
        Route::get('/', [PisoController::class, 'index']);
        Route::get('/{piso}', [PisoController::class, 'show']);
        Route::post('/', [PisoController::class, 'store']);
        Route::put('/{piso}', [PisoController::class, 'update']);
        Route::delete('/{piso}', [PisoController::class, 'destroy']);
    });

    #TIPOS DE HABITACION -> BACKEND
    Route::prefix('tipo-habitacion')->group(function () {
        Route::get('/', [TipoHabitacionController::class, 'index']);
        Route::get('/{tipoHabitacion}', [TipoHabitacionController::class, 'show']);
        Route::post('/', [TipoHabitacionController::class, 'store']);
        Route::put('/{tipoHabitacion}', [TipoHabitacionController::class, 'update']);
        Route::delete('/{tipoHabitacion}', [TipoHabitacionController::class, 'destroy']);
    });

    #USO DE HABITACIONES -> BACKEND
    Route::prefix('uso-habitacion')->group(function () {
        Route::get('/', [UsoHabitacionController::class, 'index']);
        Route::get('/{usoHabitacion}', [UsoHabitacionController::class, 'show']);
        Route::post('/', [UsoHabitacionController::class, 'store']);
        Route::put('/{usoHabitacion}', [UsoHabitacionController::class, 'update']);
        Route::delete('/{usoHabitacion}', [UsoHabitacionController::class, 'destroy']);
    });
    
    #CATEGORIA -> BACKEND
    Route::prefix('categoria')->group(function(){
        Route::get('/', [CategoriaController::class, 'index'])->name('Categoria.index');
        Route::post('/',[CategoriaController::class, 'store'])->name('Categoria.store');
        Route::get('/{category}',[CategoriaController::class, 'show'])->name('Categoria.show');
        Route::put('/{category}',[CategoriaController::class, 'update'])->name('Categoria.update');
        Route::delete('/{category}',[CategoriaController::class, 'destroy'])->name('Categoria.destroy');
    });

    #USUARIOS -> BACKEND
    Route::prefix('usuarios')->group(function(){
        Route::get('/', [UsuariosController::class, 'index'])->name('usuarios.index');
        Route::post('/',[UsuariosController::class, 'store'])->name('usuarios.store');
        Route::get('/{user}',[UsuariosController::class, 'show'])->name('usuarios.show');
        Route::put('/{user}',[UsuariosController::class, 'update'])->name('usuarios.update');
        Route::delete('/{user}',[UsuariosController::class, 'destroy'])->name('usuarios.destroy');
    });
    
    #ROLES => BACKEND
    Route::prefix('rol')->group(function () {
        Route::get('/', [RolesController::class, 'index'])->name('roles.index');
        Route::get('/Permisos', [RolesController::class, 'indexPermisos'])->name('roles.indexPermisos');
        Route::post('/', [RolesController::class, 'store'])->name('roles.store');
        Route::get('/{id}', [RolesController::class, 'show'])->name('roles.show');
        Route::put('/{id}', [RolesController::class, 'update'])->name('roles.update');
        Route::delete('/{id}', [RolesController::class, 'destroy'])->name('roles.destroy');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
