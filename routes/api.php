<?php

declare(strict_types=1);

use App\AGGridDataBuilder;
use App\Http\Controllers\AthleteController;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

Route::post('messages', function(Request $request) {
	$builder = Message::leftJoin('posts', 'posts.id', '=', 'messages.post_id')
			->leftJoin('users', 'users.id', '=', 'messages.user_id');
	return AGGridDataBuilder::create($builder)
		->debug(true)
		->defaultSelects([
			'messages.id',
			'messages.message',
			'posts.title',
			'users.name',
		])
		 ->build($request)
		 ->asResponse();
});

Route::get('messages/{field}', function(Request $request, $field) {
	return DB::table('messages')->select($field)->distinct()->orderBy($field, 'asc')->pluck($field);
});
