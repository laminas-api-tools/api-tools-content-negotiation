<?php

namespace LaminasTest\ApiTools\ContentNegotiation\TestAsset;

use Laminas\EventManager\EventManagerInterface;

class EventTarget
{
    /** @var EventManagerInterface */
    public $events;

    public function getEventManager(): EventManagerInterface
    {
        return $this->events;
    }
}
