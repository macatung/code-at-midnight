<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ContentSyncController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::prefix('v1')->group(function () {
    Route::post('/content-sync', [ContentSyncController::class, 'sync'])->name('api.v1.content-sync');
});
