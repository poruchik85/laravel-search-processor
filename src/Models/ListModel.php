<?php

namespace Poruchik85\LaravelSearchProcessor\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ListModel
{

    /**
     * @param Collection|AnonymousResourceCollection $data
     * @param int $count
     * @param int $perPage
     * @param int $currentPage
     */
    public function __construct(
        public Collection|AnonymousResourceCollection $data,
        public int                                          $count,
        public int                                          $perPage,
        public int                                          $currentPage,
    )
    {

    }

    /**
     * @param string $class
     * @return $this
     */
    public function setJsonResource(string $class): static
    {
        /** @var JsonResource $class */
        $this->data = $class::collection($this->data);

        return $this;
    }
}
