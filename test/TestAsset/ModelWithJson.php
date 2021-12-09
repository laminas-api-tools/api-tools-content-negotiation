<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\ContentNegotiation\TestAsset;

use Laminas\Stdlib\JsonSerializable;

class ModelWithJson implements JsonSerializable
{
    /** @return mixed */
    public function jsonSerialize()
    {
        return ['foo' => 'bar'];
    }
}
