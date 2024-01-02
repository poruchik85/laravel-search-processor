<?php

namespace Poruchik85\LaravelSearchProcessor\Models;

class Paginator
{
    /**
     * @var int
     */
    private int $pageSize;

    /**
     * @var int
     */
    private int $pageNumber;

    public function __construct(int $pageSize, int $pageNumber)
    {
        $this->pageSize = $pageSize;
        $this->pageNumber = $pageNumber;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @return int
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }
}
