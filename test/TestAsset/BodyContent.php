<?php

namespace LaminasTest\ApiTools\ContentNegotiation\TestAsset;

use function json_encode;

class BodyContent
{
    public function __toString(): string
    {
        return json_encode(['foo' => 'bar']);
    }
}
