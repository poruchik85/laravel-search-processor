<?php

namespace Poruchik85\LaravelSearchProcessor\Models;

class SearchFrame
{
    /**
     * @var array
     */
    private array $filters;

    /**
     * @var array
     */
    private array $sort;

    /**
     * @var Paginator
     */
    private Paginator $paginator;

    /**
     * @param array $filters
     * @param array $sort
     * @param Paginator $paginator
     */
    public function __construct(
        array $filters,
        array $sort,
        Paginator $paginator
    )
    {
        $this->filters = $filters;
        $this->sort = $sort;
        $this->paginator = $paginator;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return array
     */
    public function getSort(): array
    {
        return $this->sort;
    }

    /**
     * @return Paginator
     */
    public function getPaginator(): Paginator
    {
        return $this->paginator;
    }
}
