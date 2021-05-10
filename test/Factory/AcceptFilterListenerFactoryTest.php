<?php

namespace LaminasTest\ApiTools\ContentNegotiation\Factory;

use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;
use Laminas\ApiTools\ContentNegotiation\Factory\AcceptFilterListenerFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class AcceptFilterListenerFactoryTest extends TestCase
{
    public function testCreateServiceShouldReturnAcceptFilterListenerInstance()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions',
            new ContentNegotiationOptions()
        );

        $factory = new AcceptFilterListenerFactory();

        $service = $factory($serviceManager, 'AcceptFilterListener');

        $this->assertInstanceOf('Laminas\ApiTools\ContentNegotiation\AcceptFilterListener', $service);
    }
}
