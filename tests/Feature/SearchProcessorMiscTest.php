<?php

namespace Poruchik85\LaravelSearchProcessor\Test\Feature;

use Illuminate\Support\Facades\DB;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Models\TestModelMain;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Requests\SearchTestModelMainRequest;

class SearchProcessorMiscTest extends FeatureTestCase
{
    private const TOTAL_RECORDS = 50;
    private const PAGE_SIZE = 20;
    
    /**
     * @test
     */
    public function pagination()
    {
        $lastPage = ceil(static::TOTAL_RECORDS / static::PAGE_SIZE) - 1;
        $lastPageCount = static::TOTAL_RECORDS % static::PAGE_SIZE;
        
        $request = new SearchTestModelMainRequest([]);
        $result = $this->service->search($request);

        $this->assertEquals(static::TOTAL_RECORDS, $result->count);
        $this->assertCount(static::TOTAL_RECORDS, $result->data);

        $request = new SearchTestModelMainRequest(['page_size' => static::PAGE_SIZE]);
        $result = $this->service->search($request);

        $this->assertEquals(static::TOTAL_RECORDS, $result->count);
        $this->assertCount(static::PAGE_SIZE, $result->data);

        $request = new SearchTestModelMainRequest(['page' => $lastPage, 'page_size' => static::PAGE_SIZE]);
        $result = $this->service->search($request);

        $this->assertEquals(static::TOTAL_RECORDS, $result->count);
        $this->assertCount($lastPageCount, $result->data);
    }

    /**
     * @test
     */
    public function sorting()
    {
        $expected = TestModelMain::query()
            ->with('testModelDictionary')
            ->join('test_model_dictionary', 'test_model_dictionary.id', '=', 'test_model_main.test_model_dictionary_id')
            ->orderBy('test_model_dictionary.code')
            ->orderByDesc(DB::raw(sprintf('"test_model_main".int_field + "test_model_main".float_field')))
            ->pluck('test_model_main.id')
            ->toArray();
        
        $request = new SearchTestModelMainRequest(['sort' => ['test_model_dictionary.code', '-custom_sort']]);
        $result = $this->service->search($request)->data->pluck('id')->toArray();
        
        $this->assertEquals($expected, $result);
    }
}