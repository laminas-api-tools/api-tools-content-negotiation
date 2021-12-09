<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\ContentNegotiation\TestAsset;

use function json_encode;

class BodyContent
{
    public function __toString(): string
    {
        return (string) json_encode(['foo' => 'bar']);
    }
}
