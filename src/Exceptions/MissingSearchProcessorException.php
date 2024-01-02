<?php

namespace Poruchik85\LaravelSearchProcessor\Exceptions;

use Exception;

class MissingSearchProcessorException extends Exception
{
    protected $message = 'Missing search processor for request';
}
