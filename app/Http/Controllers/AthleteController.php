<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\AGGridDataBuilder;
use App\Models\Athlete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AthleteController extends Controller {
	public function getSetFilterValues(Request $request, $field) {
		$values = DB::table('athletes')->select($field)->distinct()->orderBy($field, 'asc')->pluck($field);
		return $values;
	}

	public function getData(Request $request) {
		return AGGridDataBuilder::create(Athlete::class)
			->build($request)
			->asResponse();
	}
}
