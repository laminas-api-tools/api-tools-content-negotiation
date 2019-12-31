<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation\Factory;

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

        $this->assertInstanceOf('Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions', $service);
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
