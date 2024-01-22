<?php

namespace Poruchik85\LaravelSearchProcessor\Test\Feature;

use Poruchik85\LaravelSearchProcessor\Exceptions\MissingSearchProcessorException;
use Poruchik85\LaravelSearchProcessor\Models\ListModel;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Requests\SearchTestModelMainRequest;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Requests\SearchTestModelOtherRequest;

class SearchProcessorServiceTest extends FeatureTestCase
{
    /**
     * @test
     */
    public function resolve_processor()
    {
        $request = new SearchTestModelMainRequest();
        $result = $this->service->search($request);
        
        $this->assertInstanceOf(ListModel::class, $result);
    }

    /**
     * @test
     */
    public function resolve_processor_invalid_request()
    {
        $request = new SearchTestModelOtherRequest();
        $this->expectException(MissingSearchProcessorException::class);

        $this->service->search($request);
    }
}