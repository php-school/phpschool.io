<?php

declare(strict_types=1);

namespace PhpSchool\Website;

use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;

/**
 * Doesn't log 4xx errors like 404 and 405, bots probing for URLs filled the log with them.
 */
class ErrorHandler extends SlimErrorHandler
{
    protected function writeToErrorLog(): void
    {
        if ($this->exception instanceof HttpException && $this->exception->getCode() < 500) {
            return;
        }

        parent::writeToErrorLog();
    }
}
