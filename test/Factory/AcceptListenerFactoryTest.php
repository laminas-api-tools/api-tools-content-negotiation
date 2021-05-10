<?php

namespace LaminasTest\ApiTools\ContentNegotiation\Factory;

use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;
use Laminas\ApiTools\ContentNegotiation\Factory\AcceptListenerFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class AcceptListenerFactoryTest extends TestCase
{
    public function testCreateServiceShouldReturnAcceptListenerInstance()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions',
            new ContentNegotiationOptions()
        );

        $factory = new AcceptListenerFactory();

        $service = $factory($serviceManager, 'AcceptListener');

        $this->assertInstanceOf('Laminas\ApiTools\ContentNegotiation\AcceptListener', $service);
    }
}
