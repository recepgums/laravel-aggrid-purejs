<?php

use App\Http\Controllers\AthleteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('olympicWinners', [AthleteController::class, 'getData'])->name('getData');

Route::get('olympicWinners/{field}', [AthleteController::class, 'getSetFilterValues'])->name('getValues');