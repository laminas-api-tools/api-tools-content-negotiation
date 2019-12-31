<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation\TestAsset;

use Laminas\Stdlib\JsonSerializable;

class ModelWithJson implements JsonSerializable
{
    public function jsonSerialize()
    {
        return ['foo' => 'bar'];
    }
}
