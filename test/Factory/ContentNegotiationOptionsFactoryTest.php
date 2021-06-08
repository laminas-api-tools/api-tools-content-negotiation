<?php

namespace LaminasTest\ApiTools\ContentNegotiation\Factory;

use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;
use Laminas\ApiTools\ContentNegotiation\Factory\ContentNegotiationOptionsFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class ContentNegotiationOptionsFactoryTest extends TestCase
{
    public function testCreateServiceShouldReturnContentNegotiationOptionsInstance()
    {
        $config = [
            'api-tools-content-negotiation' => [
                'accept_whitelist' => [],
            ],
        ];

        $serviceManager = new ServiceManager();
        $serviceManager->setService('config', $config);

        $factory = new ContentNegotiationOptionsFactory();

        $service = $factory($serviceManager, 'ContentNegotiationOptions');

        $this->assertInstanceOf(ContentNegotiationOptions::class, $service);
    }

    public function testCreateServiceShouldReturnContentNegotiationOptionsInstanceWithOptions()
    {
        $config = [
            'api-tools-content-negotiation' => [
                'accept_whitelist' => [],
            ],
        ];

        $serviceManager = new ServiceManager();
        $serviceManager->setService('config', $config);

        $factory = new ContentNegotiationOptionsFactory();

        $service = $factory($serviceManager, 'ContentNegotiationOptions');

        $this->assertNotEmpty($service->toArray());
    }

    public function testCreateServiceWithoutConfigShouldReturnContentNegotiationOptionsInstance()
    {
        $serviceManager = new ServiceManager();

        $factory = new ContentNegotiationOptionsFactory();

        $service = $factory($serviceManager, 'ContentNegotiationOptions');

        $this->assertNotEmpty($service->toArray());
    }
}
