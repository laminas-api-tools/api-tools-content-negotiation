<?php

namespace LaminasTest\ApiTools\ContentNegotiation\Factory;

use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;
use Laminas\ApiTools\ContentNegotiation\Factory\ContentTypeFilterListenerFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class ContentTypeFilterListenerFactoryTest extends TestCase
{
    public function testCreateServiceShouldReturnContentTypeFilterListenerInstance()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            'Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions',
            new ContentNegotiationOptions()
        );

        $factory = new ContentTypeFilterListenerFactory();

        $service = $factory($serviceManager, 'ContentTypeFilterListener');

        $this->assertInstanceOf('Laminas\ApiTools\ContentNegotiation\ContentTypeFilterListener', $service);
    }
}
