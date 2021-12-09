<?php

declare(strict_types=1);

namespace Laminas\ApiTools\ContentNegotiation\Exception;

use RuntimeException;

class InvalidJsonException extends RuntimeException implements ExceptionInterface
{
}
