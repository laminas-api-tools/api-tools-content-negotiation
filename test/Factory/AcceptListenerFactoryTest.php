<?php

namespace LaminasTest\ApiTools\ContentNegotiation\Factory;

use Laminas\ApiTools\ContentNegotiation\AcceptListener;
use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;
use Laminas\ApiTools\ContentNegotiation\Factory\AcceptListenerFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class AcceptListenerFactoryTest extends TestCase
{
    public function testCreateServiceShouldReturnAcceptListenerInstance(): void
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            ContentNegotiationOptions::class,
            new ContentNegotiationOptions()
        );

        $factory = new AcceptListenerFactory();

        $service = $factory($serviceManager, 'AcceptListener');

        $this->assertInstanceOf(AcceptListener::class, $service);
    }
}
