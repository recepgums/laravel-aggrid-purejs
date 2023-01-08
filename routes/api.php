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
	$builder = Message::join('posts', 'posts.id', '=', 'messages.post_id')
			->join('users', 'users.id', '=', 'posts.user_id')
		->select([
			'messages.id as messages_id',
			'messages.message as messages_message',
			'users.name as users_name',
			'posts.title as posts_title',
		]);
	return AGGridDataBuilder::create($builder)->build($request)->map(function($data) {
		$grouped = [];
		foreach ($data->toArray() as $key => $value) {
			if (str_contains($key, '_')) {
				$parts = explode('_', $key);
				$grouped[$parts[0]][$parts[1]] = $value;
			} else {
				$grouped[$key] = $value;
			}
		}
		return $grouped;
	})->asResponse();
});

Route::get('messages/{field}', function(Request $request, $field) {
	return DB::table('messages')->select($field)->distinct()->orderBy($field, 'asc')->pluck($field);
});
