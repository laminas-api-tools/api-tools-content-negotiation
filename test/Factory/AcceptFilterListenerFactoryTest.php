<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\ContentNegotiation\Factory;

use Laminas\ApiTools\ContentNegotiation\AcceptFilterListener;
use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;
use Laminas\ApiTools\ContentNegotiation\Factory\AcceptFilterListenerFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class AcceptFilterListenerFactoryTest extends TestCase
{
    public function testCreateServiceShouldReturnAcceptFilterListenerInstance(): void
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            ContentNegotiationOptions::class,
            new ContentNegotiationOptions()
        );

        $factory = new AcceptFilterListenerFactory();

        $service = $factory($serviceManager, 'AcceptFilterListener');

        $this->assertInstanceOf(AcceptFilterListener::class, $service);
    }
}
