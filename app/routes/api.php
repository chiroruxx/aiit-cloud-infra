<?php

declare(strict_types=1);

use App\Http\Controllers\ImageController;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\PublicKeyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('1.0')->group(function (): void {
    Route::apiResource('instances', InstanceController::class)->except(['update']);
    Route::apiResource('images', ImageController::class)->only(['index']);
    Route::apiResource('keys', PublicKeyController::class);
});
