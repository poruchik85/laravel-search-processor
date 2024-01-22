<?php

namespace Poruchik85\LaravelSearchProcessor\Test\Feature;

use DateInterval;
use DateTimeImmutable;
use Exception;
use Illuminate\Support\Facades\DB;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Models\TestModelDictionary;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Models\TestModelItem;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Models\TestModelMain;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Requests\SearchTestModelMainRequest;

class SearchProcessorSearchTest extends FeatureTestCase
{
    /**
     * @test
     */
    public function search_by_string()
    {
        $first = TestModelMain::first();
        $namePart = explode(' ', $first->string_field)[0];
        
        $expected = TestModelMain::where('string_field', 'like', '%' . $namePart . '%')->get();
        
        $request = new SearchTestModelMainRequest(['string_field' => $namePart]);
        $result = $this->service->search($request);
        
        $this->assertEquals(count($expected), $result->count);
        $this->assertCount(count($expected), $result->data);
    }

    /**
     * @test
     */
    public function search_by_integer()
    {
        $max = DB::table('test_model_main')->max('int_field') - 1;
        $min = DB::table('test_model_main')->min('int_field') + 1;
        $maxCond = ['int_field', '<=', $max];
        $minCond = ['int_field', '>=', $min];


        $expectedExact = TestModelMain::where('int_field', $max)->get();
        
        $request = new SearchTestModelMainRequest(['int_field' => $max]);
        $result = $this->service->search($request);

        $this->assertEquals(count($expectedExact), $result->count);
        $this->assertCount(count($expectedExact), $result->data);


        $expectedMax = TestModelMain::where([
            $maxCond,
        ])->get();

        $request = new SearchTestModelMainRequest(['int_interval' => [null, $max]]);
        $result = $this->service->search($request);
        
        $this->assertEquals(count($expectedMax), $result->count);
        $this->assertCount(count($expectedMax), $result->data);

        
        $expectedMin = TestModelMain::where([
            $minCond,
        ])->get();

        $request = new SearchTestModelMainRequest(['int_interval' => [$min, null]]);
        $result = $this->service->search($request);
        
        $this->assertEquals(count($expectedMin), $result->count);
        $this->assertCount(count($expectedMin), $result->data);

        
        $expectedBoth = TestModelMain::where([
            $maxCond,
            $minCond,
        ])->get();

        $request = new SearchTestModelMainRequest(['int_interval' => [$min, $max]]);
        $result = $this->service->search($request);

        $this->assertEquals(count($expectedBoth), $result->count);
        $this->assertCount(count($expectedBoth), $result->data);
    }

    /**
     * @test
     */
    public function search_by_float()
    {
        $max = DB::table('test_model_main')->max('float_field') - 1;
        $min = DB::table('test_model_main')->min('float_field') + 1;
        $maxCond = ['float_field', '<=', $max];
        $minCond = ['float_field', '>=', $min];


        $expectedExact = TestModelMain::where('float_field', $max)->get();

        $request = new SearchTestModelMainRequest(['float_field' => $max]);
        $result = $this->service->search($request);

        $this->assertEquals(count($expectedExact), $result->count);
        $this->assertCount(count($expectedExact), $result->data);


        $expectedMax = TestModelMain::where([
            $maxCond,
        ])->get();

        $request = new SearchTestModelMainRequest(['float_interval' => [null, $max]]);
        $result = $this->service->search($request);

        $this->assertEquals(count($expectedMax), $result->count);
        $this->assertCount(count($expectedMax), $result->data);


        $expectedMin = TestModelMain::where([
            $minCond,
        ])->get();

        $request = new SearchTestModelMainRequest(['float_interval' => [$min, null]]);
        $result = $this->service->search($request);

        $this->assertEquals(count($expectedMin), $result->count);
        $this->assertCount(count($expectedMin), $result->data);


        $expectedBoth = TestModelMain::where([
            $maxCond,
            $minCond,
        ])->get();

        $request = new SearchTestModelMainRequest(['float_interval' => [$min, $max]]);
        $result = $this->service->search($request);

        $this->assertEquals(count($expectedBoth), $result->count);
        $this->assertCount(count($expectedBoth), $result->data);
    }

    /**
     * @test
     */
    public function search_by_bool()
    {
        $expectedTrue = TestModelMain::where('bool_field', true)->get();
        
        $request = new SearchTestModelMainRequest(['bool_field' => '1']);
        $result = $this->service->search($request);
        
        $this->assertEquals(count($expectedTrue), $result->count);
        $this->assertCount(count($expectedTrue), $result->data);

        
        $expectedFalse = TestModelMain::where('bool_field', false)->get();

        $request = new SearchTestModelMainRequest(['bool_field' => '0']);
        $result = $this->service->search($request);

        $this->assertEquals(count($expectedFalse), $result->count);
        $this->assertCount(count($expectedFalse), $result->data);
    }

    /**
     * @test
     */
    public function search_by_list()
    {
        $average = round(DB::table('test_model_main')->average('int_field'));
        $list = DB::table('test_model_main')->where('int_field', '>=', $average)->pluck('int_field')->toArray();

        $expected = TestModelMain::whereIn('int_field', $list)->get();
        
        $request = new SearchTestModelMainRequest(['int_field_list' => $list]);
        $result = $this->service->search($request);

        $this->assertEquals(count($expected), $result->count);
        $this->assertCount(count($expected), $result->data);
    }

    /**
     * @test
     * @throws Exception
     */
    public function search_by_date()
    {
        $maxTotal = new DateTimeImmutable(DB::table('test_model_main')->max('time_field'));
        $minTotal = new DateTimeImmutable(DB::table('test_model_main')->min('time_field'));

        $half = new DateInterval(sprintf('P%dD', round($minTotal->diff($maxTotal)->days / 2)));
        $min = $maxTotal->sub($half)->format('Y-m-d 00:00:00');
        $max = $maxTotal->sub(new DateInterval('P1D'))->format('Y-m-d 23:59:59');

        $maxCond = ['time_field', '<=', $max];
        $minCond = ['time_field', '>=', $min];
        
        $expectedExact = TestModelMain::where([
            ['time_field', '>=', $maxTotal->format('Y-m-d 00:00:00')],
            ['time_field', '<=', $maxTotal->format('Y-m-d 23:59:59')]
        ])->get();

        $request = new SearchTestModelMainRequest(['time_field' => [$maxTotal->format('Y-m-d')]]);
        $result = $this->service->search($request);

        $this->assertEquals(count($expectedExact), $result->count);
        $this->assertCount(count($expectedExact), $result->data);

        
        $expectedMax = TestModelMain::where([
            $maxCond,
        ])->get();

        $request = new SearchTestModelMainRequest(['time_field' => [null, $max]]);
        $result = $this->service->search($request);
        
        $this->assertEquals(count($expectedMax), $result->count);
        $this->assertCount(count($expectedMax), $result->data);

        
        $expectedMin = TestModelMain::where([
            $minCond,
        ])->get();

        $request = new SearchTestModelMainRequest(['time_field' => [$min, null]]);
        $result = $this->service->search($request);
        
        $this->assertEquals(count($expectedMin), $result->count);
        $this->assertCount(count($expectedMin), $result->data);

        
        $expectedBoth = TestModelMain::where([
            $maxCond,
            $minCond,
        ])->get();

        $request = new SearchTestModelMainRequest(['time_field' => [$min, $max]]);
        $result = $this->service->search($request);

        $this->assertEquals(count($expectedBoth), $result->count);
        $this->assertCount(count($expectedBoth), $result->data);
    }

    /**
     * @test
     */
    public function search_custom()
    {
        $module = rand(2, 5);

        $expected = TestModelMain::whereRaw('mod(int_field, ' . $module . ') = 0')->get();

        $request = new SearchTestModelMainRequest(['int_field_custom' => $module]);
        $result = $this->service->search($request);

        $this->assertEquals(count($expected), $result->count);
        $this->assertCount(count($expected), $result->data);
    }

    /**
     * @test
     */
    public function search_by_binding()
    {
        $dictionaryItem = TestModelDictionary::inRandomOrder()->first();
        
        $expected = TestModelMain::where('test_model_dictionary_id', $dictionaryItem->id)->get();

        $request = new SearchTestModelMainRequest(['transit_entity_filter' => $dictionaryItem->code]);
        $result = $this->service->search($request);

        $this->assertEquals(count($expected), $result->count);
        $this->assertCount(count($expected), $result->data);
    }

    /**
     * @test
     */
    public function search_by_binding_aggregate()
    {
        $item = TestModelItem::inRandomOrder()->first();
        $searchWord = explode(' ', $item->name)[1];
        
        $expected = DB::table('test_model_item')->selectRaw('distinct test_model_main_id')->where('name', 'like', '%' . $searchWord . '%')->get();

        $request = new SearchTestModelMainRequest(['transit_entity_agg_filter' => $searchWord]);
        $result = $this->service->search($request);

        $this->assertEquals(count($expected), $result->count);
        $this->assertCount(count($expected), $result->data);
    }
}