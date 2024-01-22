<?php

namespace Poruchik85\LaravelSearchProcessor\Test\TestData\Services;

use Illuminate\Support\Facades\DB;
use Poruchik85\LaravelSearchProcessor\Services\SearchProcessor;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Models\TestModelMain;

class TestModelMainSearchProcessor extends SearchProcessor
{
    protected const DEFAULT_PAGE_SIZE = 50;

    protected const DEFAULT_SORT = [
        [
            'by' => 'int_field',
            'direction' => SORT_DESC
        ]
    ];

    /**
     * @inheritdoc
     */
    protected function mainQuery()
    {
        return TestModelMain::query()
            ->select([
                'test_model_main.id',
                'string_field',
                'int_field',
                'float_field',
                'bool_field',
                'time_field',
                'test_model_dictionary_id',
            ])
            ->leftJoin('test_model_dictionary', 'test_model_dictionary.id', '=', 'test_model_main.test_model_dictionary_id')
            ->with('testModelItems');
    }

    /**
     * @inheritdoc
     */
    protected function filters(): array
    {
        return [
            'string_field' => [
                'handler' => 'text',
            ],
            'int_field' => [
                'handler' => 'number',
            ],
            'int_interval' => [
                'handler' => 'number',
                'interval' => true,
                'field' => 'int_field',
            ],
            'float_field' => [
                'handler' => 'number',
            ],
            'float_interval' => [
                'handler' => 'number',
                'interval' => true,
                'field' => 'float_field',
            ],
            'bool_field' => [
                'handler' => 'bool',
            ],
            'time_field' => [
                'handler' => 'date',
            ],
            'int_field_list' => [
                'handler' => 'list',
                'field' => 'int_field',
            ],
            'int_field_custom' => [
                'handler' => fn($builder, $value) => $builder->where(function ($query) use ($value) {
                    $query->whereRaw('mod(' . $this->mainTable() . '.int_field, ' . $value . ') = 0');
                }),
            ],
            'transit_entity_filter' => [
                'handler' => 'string',
                'field' => 'test_model_dictionary.code'
            ],
            'transit_entity_agg_filter' => [
                'handler' => fn($builder, $value) => $builder->whereExists(function ($query) use ($value) {
                    $query->select(DB::raw(1))
                        ->from('test_model_item')
                        ->where('test_model_item.name', 'like', '%' . $value . '%')
                        ->whereColumn('test_model_item.test_model_main_id', 'test_model_main.id');
                }),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function mainTable(): string
    {
        return 'test_model_main';
    }

    /**
     * @inheritdoc
     */
    protected function sortMapping(): array
    {
        return [
            'custom_sort' => DB::raw(sprintf('"%1$s".int_field + "%1$s".float_field', $this->mainTable())),
        ];
    }
}
