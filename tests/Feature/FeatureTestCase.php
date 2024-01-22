<?php

namespace Poruchik85\LaravelSearchProcessor\Test\Feature;

use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;
use Poruchik85\LaravelSearchProcessor\Services\SearchProcessorService;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Models\TestModelDictionary;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Models\TestModelItem;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Models\TestModelMain;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Requests\SearchTestModelMainRequest;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Services\TestModelMainSearchProcessor;

abstract class FeatureTestCase extends TestCase
{
    protected SearchProcessorService $service;
    
    public function setUp(): void
    {
        parent::setUp();
        
        $this->service = App::make(SearchProcessorService::class);
        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            'Poruchik85\LaravelSearchProcessor\Providers\LaravelSearchProcessorServiceProvider',
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('search_processor.request_map', [
            SearchTestModelMainRequest::class => TestModelMainSearchProcessor::class
        ]);

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUpDatabase()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../TestData/Database/Migrations');
        
        TestModelDictionary::factory()->count(3)->create();
        TestModelMain::factory()
            ->has(TestModelItem::factory()->count(5))
            ->count(50)->create();
    }
}
