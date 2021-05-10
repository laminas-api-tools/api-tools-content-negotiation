<?php

namespace LaminasTest\ApiTools\ContentNegotiation\TestAsset;

class EventTarget
{
    public $events;

    public function getEventManager()
    {
        return $this->events;
    }
}
