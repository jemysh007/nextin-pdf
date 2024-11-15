<?php

use App\Http\Controllers\DemoController;
use App\Http\Controllers\JDController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get("/demo", [DemoController::class, 'index']);
Route::get("/remove-refined", [JDController::class, 'removeRefined']);
