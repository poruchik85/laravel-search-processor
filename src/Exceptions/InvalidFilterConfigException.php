<?php

namespace Poruchik85\LaravelSearchProcessor\Exceptions;

use Exception;

class InvalidFilterConfigException extends Exception
{
    public function __construct($message)
    {
        parent::__construct(sprintf(
            "Invalid filter configuration: %s",
            $message,
        ));
    }
}
