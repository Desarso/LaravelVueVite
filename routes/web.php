<?php

// use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
// use App\Http\Controllers\Config\AppController;

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
// //Login
// Auth::routes();
// //Login


Route::get('/', function () {
    return inertia('welcome');
});

Route::namespace ('App\Http\Controllers\Config')->group(function () {
    // Route::get('/config-apps', [AppController::class, 'index']);
    Route::get('/config-apps', 'AppController@index');

});
