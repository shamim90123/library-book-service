<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DBController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Book\BookController;
use Illuminate\Support\Facades\Redis;
use App\Http\Middleware\CheckStatus;

use App\Http\Controllers\GeoLocationController;
use App\Http\Controllers\PostGuzzleController;
use App\Http\Controllers\Polymorphic\PostController;
use App\Http\Controllers\ProvisionServer;

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

Route::middleware([CheckStatus::class])->group(function(){

    Route::prefix('book')->group(function () {
        Route::get('/books', [BookController::class, 'index'])->name('books');
        Route::post('/book-store', [BookController::class, 'store'])->name('book-store');
    });

    Route::prefix('db')->group(function () {
        Route::get('/db-backup', [DBController::class, 'databaseBackup']);
        Route::get('/user', [UsersController::class, 'findUser']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/find-user', [UsersController::class, 'findUser']);
    });

    Route::prefix('redis')->group(function () {
        Route::get('/redis', function () {
            $visits = Redis::incr('visits');
            $get = Redis::get('visits');
            return $visits;
        });
    });

    // json data routes
    Route::prefix('json')->group(function () {
        Route::get('/posts',[PostGuzzleController::class,'index']);
        Route::get('/posts/store', [PostGuzzleController::class, 'store' ]);
    });

    // language change by param
    Route::prefix('locale')->group(function () {
        Route::get('/{locale?}', function ($locale = null) {
            if (isset($locale) && in_array($locale, config('app.available_locales'))) {
                app()->setLocale($locale);
            }
            return view('welcome');
        });
    });

    // ip localion
    Route::prefix('ip-info')->group(function () {
        Route::get('get-address-from-ip', [GeoLocationController::class, 'index']);
    });

    // server localion
    Route::prefix('server')->group(function () {
        Route::get('/info', [ProvisionServer::class]);
    });

    Route::prefix('test')->group(function () {
        Route::get('get-posts', [PostController::class, 'index']);
    });

});



