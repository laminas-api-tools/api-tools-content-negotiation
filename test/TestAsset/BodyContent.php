<?php

namespace LaminasTest\ApiTools\ContentNegotiation\TestAsset;

class BodyContent
{
    public function __toString()
    {
        return json_encode(['foo' => 'bar']);
    }
}
