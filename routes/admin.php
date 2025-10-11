<?php

use Azuriom\Plugin\ApiExtender\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your plugin. These
| routes are loaded by the RouteServiceProvider of your plugin within
| a group which contains the "web" middleware group and your plugin name
| as prefix. Now create something great!
|
*/
Route::get('/', [AdminController::class, 'index'])->name('index');
Route::get('/images', [AdminController::class, 'images'])->name('images');
Route::middleware('can:apiextender.keys')->group(function () {
  
    Route::prefix('api-keys')->name('api-keys.')->group(function () {
        Route::get('/', [AdminController::class, 'apikeys'])->name('index');
        Route::post('/generate', [AdminController::class, 'generateApiKey'])->name('generate');
        Route::post('/{apiKey}/toggle', [AdminController::class, 'toggleApiKey'])->name('toggle');
        Route::delete('/{apiKey}', [AdminController::class, 'deleteApiKey'])->name('delete');
    });
});

Route::middleware('can:apiextender.cron')->group(function () {
    Route::get('/cron', [AdminController::class, 'cron'])->name('cron');
    Route::post('/cron/toggle', [AdminController::class, 'toggleCron'])->name('cron.toggle');
    Route::post('/cron/test', [AdminController::class, 'testCron'])->name('cron.test');
});