<?php

namespace Poruchik85\LaravelSearchProcessor\Services;

use Poruchik85\LaravelSearchProcessor\Exceptions\InvalidFilterConfigException;
use Poruchik85\LaravelSearchProcessor\Models\ListModel;
use Poruchik85\LaravelSearchProcessor\Models\Paginator;
use Poruchik85\LaravelSearchProcessor\Models\SearchFrame;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

abstract class SearchProcessor
{
    protected const DEFAULT_PAGE_SIZE = 20;
    protected const MAX_PAGE_SIZE = 200;
    protected const DEFAULT_SORT = [];

    protected const NULL_DATE_VALUE = 'null';
    
    protected const LOGICAL_SYMBOL_OR = 'or';
    protected const LOGICAL_SYMBOL_AND = 'and';

    /**
     * @var SearchFrame
     */
    protected SearchFrame $searchFrame;

    /**
     * @return ListModel
     * @throws InvalidFilterConfigException
     */
    final public function search(): ListModel
    {
        $query = $this->mainQuery();

        $count = $this->wrapQuery($query);
        $data = $query->get();

        return new ListModel($data, $count, $this->searchFrame->getPaginator()->getPageSize(), $this->searchFrame->getPaginator()->getPageNumber());
    }

    /**
     * @param FormRequest $request
     */
    final public function __construct(FormRequest $request)
    {
        $requestData = $request->all();

        $sort = $this->parseSort($requestData);

        $pageSize = min($requestData['page_size'] ?? static::DEFAULT_PAGE_SIZE, static::MAX_PAGE_SIZE);
        $paginator = new Paginator(
            $pageSize,
            $requestData['page'] ?? 0,
        );


        $filters = array_filter(
            $requestData,
            static fn($key) => !in_array($key, ['page_size', 'page', 'sort']),
            ARRAY_FILTER_USE_KEY,
        );

        $this->searchFrame = new SearchFrame(
            $filters,
            $sort,
            $paginator,
        );

        $this->user = $request->user();
    }


    /**
     * @param array $requestData
     * @return array
     */
    private function parseSort(array $requestData): array
    {
        $sort = static::DEFAULT_SORT;

        if (!empty($requestData['sort']) && is_string($requestData['sort'])) {
            $requestData['sort'] = [
                $requestData['sort']
            ];
        }

        if (!empty($requestData['sort'])) {
            $sort = [];
            foreach ($requestData['sort'] as $item) {
                if (str_starts_with($item, '-')) {
                    $sort[] = [
                        'by' => substr($item, 1),
                        'direction' => SORT_DESC
                    ];
                } else {
                    $sort[] = [
                        'by' => $item,
                        'direction' => SORT_ASC
                    ];
                }
            }

        }

        return $sort;
    }

    /**
     * @param mixed $builder
     * @return int
     * @throws InvalidFilterConfigException
     */
    protected function wrapQuery(&$builder): int
    {
        $this->addFilters($builder);
        $this->credentialsFilters($builder);

        $count = $builder->count();
        $this->addSort($builder);
        $this->addPagination($builder);

        return $count;
    }

    /**
     * @return Builder
     * @throws InvalidFilterConfigException
     */
    public function getQuery(): Builder
    {
        $query = $this->mainQuery();
        $this->addFilters($query);
        $this->credentialsFilters($query);

        $this->addSort($query);

        return $query;
    }

    /**
     * @param mixed $builder
     */
    private function addSort($builder): void
    {
        $sortMapping = $this->sortMapping();
        foreach ($this->searchFrame->getSort() as $sortItem) {
            $sortExpression = $sortMapping[$sortItem['by']] ?? $sortItem['by'];

            if ($sortItem['direction'] === SORT_DESC) {
                $builder->orderByDesc($sortExpression);
            } elseif ($sortItem['direction'] === SORT_ASC) {
                $builder->orderBy($sortExpression);
            }
        }
    }

    /**
     * @param mixed $builder
     * @throws InvalidFilterConfigException
     */
    protected function addFilters($builder): void
    {
        $filterSignatures = $this->filters();
        $frameFilters = $this->searchFrame->getFilters();
        foreach ($frameFilters as $filter => $value) {
            if (!isset($filterSignatures[$filter]['handler'])) {
                continue;
            }
            
            if (isset($frameFilters[$filter . '_symbol'])) {
                $value['symbol'] = $frameFilters[$filter . '_symbol'];
            }

            if (is_string($filterSignatures[$filter]['handler'])) {
                $this->defaultFilterHandle($builder, $filter, $value);
            } elseif (is_callable($filterSignatures[$filter]['handler'])) {
                $filterSignatures[$filter]['handler']($builder, $value);
            }
        }
    }

    /**
     * @param mixed $builder
     */
    protected function credentialsFilters($builder): void
    {

    }

    /**
     * @param mixed $builder
     * @param string $filterName
     * @param mixed $value
     * @throws InvalidFilterConfigException
     */
    private function defaultFilterHandle($builder, string $filterName, $value): void
    {
        $filter = $this->filters()[$filterName];

        $field = $filter['field'] ?? $this->mainTable() . '.' . $filterName;

        switch ($filter['handler']) {
            case 'text':
            case 'string':
                $builder->where($field, 'like', '%' . $value . '%');
                break;
            case 'number':
                if ($filter['interval'] ?? false) {
                    if (isset($value[0]) && $value[0] !== null && $value[0] !== '') {
                        $builder->where($field, '>=', $value[0]);
                    }
                    if (isset($value[1]) && $value[1] !== null && $value[1] !== '') {
                        $builder->where($field, '<=', $value[1]);
                    }

                    break;
                }

                $builder->where($field, '=', $value);
                break;
            case 'bool':
                if ($value === 1 || $value === '1') {
                    $builder->where($field, true);
                } elseif ($value === 0 || $value === '0') {
                    $builder->where($field, false);
                }

                break;
            case 'date':
                if (count($value) === 1) {
                    $startDate = Carbon::parse($value[0])->startOfDay();
                    $endDate = Carbon::parse($value[0])->endOfDay();
                    $builder->whereBetween($field, [$startDate, $endDate]);
                }
                if (count($value) === 2) {
                    if ($value[0] !== static::NULL_DATE_VALUE && $value[0] !== null) {
                        $startDate = Carbon::parse($value[0])->startOfDay();
                        $builder->where($field, '>=', $startDate);
                    }
                    if ($value[1] !== static::NULL_DATE_VALUE && $value[1] !== null) {
                        $endDate = Carbon::parse($value[1])->endOfDay();
                        $builder->where($field, '<=', $endDate);
                    }
                }

                break;
            case 'list':
                if (is_array($value)) {
                    $builder->whereIn($field, $value);

                    break;
                }

                $builder->where($field, '=', $value);
                break;
            case 'advanced_list':
                if (!$filter['pivot_table']) {
                    throw new InvalidFilterConfigException('pivot_table for advanced_list filter not specified');
                }

                $pivotTable = $filter['pivot_table'];
                $mainField = $filter['main_field'] ?? $this->mainTable() . '_id';
                $referenceField = $filter['reference_field'] ?? $filterName;
                
                if (!is_array($value)) {
                    $value = [$value];
                }
                $value = array_unique($value);
                
                if (isset($value['symbol'])) {
                    $symbol = strtolower($value['symbol']);
                    unset($value['symbol']);
                } else {
                    $symbol = static::LOGICAL_SYMBOL_OR;
                }
                
                if (!in_array($symbol, [static::LOGICAL_SYMBOL_OR, static::LOGICAL_SYMBOL_AND])) {
                    throw new InvalidFilterConfigException(sprintf(
                        'invalid advanced_list symbol %s. Available symbols: %s',
                        $symbol,
                        implode(', ', [static::LOGICAL_SYMBOL_OR, static::LOGICAL_SYMBOL_AND])
                    ));
                }

                if ($symbol === static::LOGICAL_SYMBOL_AND) {
                    $builder->where(function ($query) use ($value, $pivotTable, $mainField, $referenceField) {
                        $query->whereIn(
                            $this->mainTable() . '.id',
                            function ($q) use ($value, $pivotTable, $mainField, $referenceField) {
                                $q
                                    ->select($mainField)
                                    ->from($pivotTable)
                                    ->whereIntegerInRaw($referenceField, $value)
                                    ->groupBy($mainField)
                                    ->havingRaw('count(*) = ' . count($value))
                                ;
                            }
                        );
                    });
                } else if ($symbol === static::LOGICAL_SYMBOL_OR) {
                    $builder->where(function ($query) use ($value, $pivotTable, $mainField, $referenceField) {
                        $query->whereIn(
                            $this->mainTable() . '.id',
                            function ($q) use ($value, $pivotTable, $mainField, $referenceField) {
                                $q->select($mainField)->from($pivotTable)->whereIntegerInRaw($referenceField, $value);
                            }
                        );
                    });
                }
                break;
            case 'equals':
            default:
                $builder->where($field, '=', $value);
                break;
        }
    }

    /**
     * @param mixed $builder
     */
    private function addPagination($builder): void
    {
        $limit = $this
            ->searchFrame
            ->getPaginator()
            ->getPageSize() ?? static::DEFAULT_PAGE_SIZE;
        $offset = ($this
                ->searchFrame
                ->getPaginator()
                ->getPageNumber() ?? 0) * $limit;
        $builder
            ->limit($limit)
            ->offset($offset);
    }

    /**
     * @return array
     */
    protected function filters(): array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function sortMapping(): array
    {
        return [];
    }

    /**
     * @return Mixed
     */
    abstract protected function mainQuery();

    /**
     * @return Mixed
     */
    abstract protected function mainTable(): string;
}
