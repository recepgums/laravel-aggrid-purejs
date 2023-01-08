<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use function array_key_exists;
use function array_push;
use function count;
use function is_null;
use function join;
use function sizeof;

/**
 * AgGridDataBuilder for Laravel Eloquent. Made by @xerenahmed
 */
class AGGridDataBuilder {
	private int $rowCount;
	private Collection $resultsForPage;
	private Collection $results;

	private function __construct(private Builder $sqlBuilder) {
	}

	public static function create(Builder $sqlBuilder) : self {
		return new self($sqlBuilder);
	}

	public function build(Request $request) : self {
		$this->applySelect($request);
		$this->applyFilters($request);
		$this->applyGrouping($request);
		$this->applySorting($request);
		$this->applyLimit($request);

		\Log::debug($this->sqlBuilder->toSql() . ' [' . join(', ', $this->sqlBuilder->getBindings()) . ']');
		$this->fetch();
		$this->rowCount = $this->initRowCount($request);
		$this->resultsForPage = $this->cutResultsToPageSize($request);
		return $this;
	}

	protected function fetch() : self {
		$this->results = $this->sqlBuilder->get();
		return $this;
	}

	public function map(callable $callback) : self {
		$this->resultsForPage = $this->resultsForPage->map($callback);
		return $this;
	}

	private function applySelect(Request $request) : self {
		$rowGroupCols = $request->input('rowGroupCols');
		$valueCols = $request->input('valueCols');
		$groupKeys = $request->input('groupKeys');

		if (!(count($rowGroupCols) > count($groupKeys))) {
			return $this;
		}

		$colsToSelect = [];

		$rowGroupCol = $rowGroupCols[sizeof($groupKeys)];
		array_push($colsToSelect, $rowGroupCol['field']);

		foreach ($valueCols as $_ => $value) {
			array_push($colsToSelect, $value['aggFunc'] . '(' . $value['field'] . ') as ' . $value['field']);
		}

		$this->sqlBuilder->select(new Expression(join(',', $colsToSelect)));
		return $this;
	}

	private function applyGrouping(Request $request) : self {
		$rowGroupCols = $request->input('rowGroupCols');
		$groupKeys = $request->input('groupKeys');

		if (count($groupKeys) >= count($rowGroupCols)) {
			return $this;
		}

		$colsToGroupBy = [];
		$rowGroupCol = $rowGroupCols[sizeof($groupKeys)];
		array_push($colsToGroupBy, $rowGroupCol['field']);

		$this->sqlBuilder->groupBy($colsToGroupBy);
		return $this;
	}

	private function applySorting(Request $request) : self {
		$sortModel = $request->input('sortModel');
		if (empty($sortModel)) {
			return $this;
		}

		foreach ($sortModel as $sort) {
			$this->sqlBuilder->orderBy($sort['colId'], $sort['sort']);
		}

		return $this;
	}

	private function applyLimit(Request $request) : self {
		$startRow = $request->input('startRow');
		$endRow = $request->input('endRow');
		$pageSize = ($endRow - $startRow) + 1;

		$this->sqlBuilder->limit($pageSize)->offset($startRow);
		return $this;
	}

	private function applyFilters(Request $request) : self {
		$filters = $request->get('filterModel');
		$rowGroupCols = $request->input('rowGroupCols');
		$groupKeys = $request->input('groupKeys');

		if (sizeof($groupKeys) > 0) {
			$groupKey = $groupKeys[sizeof($groupKeys) - 1];
			$rowGroupCol = $rowGroupCols[sizeof($groupKeys) - 1];
			$this->sqlBuilder->where($rowGroupCol['field'], $groupKey);
		}

		if (empty($filters)) {
			return $this;
		}

		foreach ($filters as $field => $data) {
			if (array_key_exists('operator', $data)) {
				$operator = $data['operator'];
				$conditionData1 = $data['condition1'];
				$conditionData2 = $data['condition2'];

				$this->sqlBuilder->where(function (Builder $query) use ($field, $conditionData1, $conditionData2, $operator) {
					$this->handleWhereFor($query, $field, $conditionData1);
					$this->handleWhereFor($query, $field, $conditionData2, $operator === 'OR');
				});
			} else {
				$this->handleWhereFor($this->sqlBuilder, $field, $data);
			}
		}

		return $this;
	}

	private function handleWhereFor(QueryBuilder|Builder $builder, string $field, array $data, bool $isOr = false) : void {
		$type = $data['type'];
		if ($type === 'inRange') {
			$builder->{$isOr ? 'orWhereBetween' : 'whereBetween'}($field, [$data['filter'], $data['filterTo']]);
			return;
		}

		if ($type === 'blank') {
			$builder->{$isOr ? 'orWhereNull' : 'whereNull'}($field);
			return;
		}

		if ($type === 'notBlank') {
			$builder->{$isOr ? 'orWhereNotNull' : 'whereNotNull'}($field);
			return;
		}

		$filter = $data['filter'];
		$value = match ($type) {
			'contains', 'notContains' => '%' . $filter . '%',
			'startsWith' => $filter . '%',
			'endsWith' => '%' . $filter,
			default => $filter,
		};

		$operator = match ($type) {
			'equals' => '=',
			'notEqual' => '!=',
			'lessThan' => '<',
			'lessThanOrEqual' => '<=',
			'greaterThan' => '>',
			'greaterThanOrEqual' => '>=',
			'contains', 'startsWith', 'endsWith' => 'LIKE',
			'notContains' => 'NOT LIKE',
			default => '=',
		};

		$builder->{$isOr ? 'orWhere' : 'where'}($field, $operator, $value);
	}

	private function initRowCount($request) {
		$results = $this->results;
		if (is_null($results) || count($results) == 0) {
			// or return null
			return 0;
		}

		$currentLastRow = $request['startRow'] + count($results);
		return $currentLastRow <= $request['endRow'] ? $currentLastRow : -1;
	}

	private function cutResultsToPageSize($request) : Collection {
		$results = $this->results;
		$pageSize = $request['endRow'] - $request['startRow'];
		if ($results && (sizeof($results) > $pageSize)) {
			return $results->slice($request['startRow'], $pageSize);
		} else {
			return $results;
		}
	}

	public function getResultsForPage() : Collection {
		return $this->resultsForPage;
	}

	public function getRowCount() : int {
		return $this->rowCount;
	}

	public function asResponse() : array {
		return [
			'lastRow' => $this->rowCount,
			'rows' => $this->resultsForPage,
		];
	}

	public function getResults() : Collection {
		return $this->results;
	}
}
