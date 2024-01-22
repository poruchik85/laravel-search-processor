<?php

namespace Poruchik85\LaravelSearchProcessor\Providers;

use Poruchik85\LaravelSearchProcessor\Services\SearchProcessorService;
use Illuminate\Support\ServiceProvider;

class LaravelSearchProcessorServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            SearchProcessorService::class,
            SearchProcessorService::class
        );
    }
    
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/search_processor.php' => config_path('search_processor.php'),
            ]);
        }
    }
}
