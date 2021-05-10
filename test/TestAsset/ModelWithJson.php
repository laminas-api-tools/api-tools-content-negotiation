<?php

namespace LaminasTest\ApiTools\ContentNegotiation\TestAsset;

use Laminas\Stdlib\JsonSerializable;

class ModelWithJson implements JsonSerializable
{
    public function jsonSerialize()
    {
        return ['foo' => 'bar'];
    }
}
