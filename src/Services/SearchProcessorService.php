<?php

namespace Poruchik85\LaravelSearchProcessor\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Poruchik85\LaravelSearchProcessor\Exceptions\InvalidFilterConfigException;
use Poruchik85\LaravelSearchProcessor\Exceptions\MissingSearchProcessorException;
use Poruchik85\LaravelSearchProcessor\Models\ListModel;

class SearchProcessorService
{
    private array $searchProcessorMap;

    public function __construct()
    {
        $this->searchProcessorMap = config('search_processor.request_map');
    }

    /**
     * @param FormRequest $request
     * @return ListModel
     * @throws MissingSearchProcessorException
     * @throws InvalidFilterConfigException
     */
    public function search(FormRequest $request): ListModel
    {
        $searchProcessor = $this->resolveProcessor($request);
        return $searchProcessor->search();
    }

    /**
     * @param FormRequest $request
     * @return Builder
     * @throws MissingSearchProcessorException
     * @throws InvalidFilterConfigException
     */
    public function getQuery(FormRequest $request): Builder
    {
        $searchProcessor = $this->resolveProcessor($request);
        return $searchProcessor->getQuery();
    }

    /**
     * @param FormRequest $request
     * @return SearchProcessor
     * @throws MissingSearchProcessorException
     */
    private function resolveProcessor(FormRequest $request): SearchProcessor
    {
        if (!array_key_exists(get_class($request), $this->searchProcessorMap)) {
            throw new MissingSearchProcessorException();
        }

        $processorClass = $this->searchProcessorMap[get_class($request)];

        return new $processorClass($request);
    }
}
