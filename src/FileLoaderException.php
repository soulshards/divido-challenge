<?php

namespace App;

/**
 *  Custom Exception sub-class, so all unrecoverable errors,
 *  from the perspective of the FileLoaderInterface implementations and their factory,
 *  are bubbled up to the client code,
 *  which in turn can decide what to do with them.
 *
 *  @codeCoverageIgnore
 */
class FileLoaderException extends \Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
