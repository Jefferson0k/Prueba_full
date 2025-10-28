<?php

use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\UsuariosController;
use App\Http\Controllers\Web\Categoria\CategoriaWeb;
use App\Http\Controllers\Web\Producto\ProductoWeb;
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

    # VISTAS DEL FRONTEND
    Route::prefix('panel')->group(function () {
        # 🔹 Categorías y productos
        Route::get('/categorias', [CategoriaWeb::class, 'view'])->name('categorias.index');
        Route::get('/productos', [ProductoWeb::class, 'view'])->name('productos.index');
    });

    #PRODUCTOS -> BACKEND
    Route::prefix('producto')->group(function(){
        Route::get('/', [ProductoController::class, 'index'])->name('Productos.index');
        Route::post('/',[ProductoController::class, 'store'])->name('Productos.store');
        Route::get('/search', [ProductoController::class, 'searchProducto'])->name('Productos.search');
        Route::get('/{product}',[ProductoController::class, 'show'])->name('Productos.show');
        Route::put('/{product}',[ProductoController::class, 'update'])->name('Productos.update');
        Route::delete('/{product}',[ProductoController::class, 'destroy'])->name('Productos.destroy');
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
        Route::get('/search/by-subranch', [UsuariosController::class, 'search'])->name('usuarios.search');
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
