<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [WebController::class, 'home'])->name('home');

Route::get('/dashboard', function () {
    return view('Admin.dashboard');
});

Route::get('/edit-user/{id}', [WebController::class, 'edit'])->name('usersedit');
Route::post('/edit-user/{id}', [WebController::class, 'edituser'])->name('edit-user');

Route::get('/recharge/{id}', [WebController::class, 'recharge']);
Route::post('/recharge/{id}', [WebController::class, 'addrecharge'])->name('recharge');


Route::get('/admin/users', [WebController::class, 'users'])->name('users');
Route::get('/create-user', [WebController::class, 'create_user'])->name('createuser');
Route::post('/create-user', [WebController::class, 'createUser'])->name('create-user');

Route::post('adminlogin', [AuthController::class, 'Adminlogin'])->name('adminlogin');
Route::get('logout', [WebController::class, 'logout'])->name('logout');
