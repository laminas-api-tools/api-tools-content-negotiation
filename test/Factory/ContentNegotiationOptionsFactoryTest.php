<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation\Factory;

use Laminas\ApiTools\ContentNegotiation\Factory\ContentNegotiationOptionsFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;

class ContentNegotiationOptionsFactoryTest extends TestCase
{
    public function testCreateServiceShouldReturnContentNegotiationOptionsInstance()
    {
        $config = array(
            'api-tools-content-negotiation' => array(
                'accept_whitelist' => array(),
            ),
        );

        $serviceManager = new ServiceManager();
        $serviceManager->setService('Config', $config);

        $factory = new ContentNegotiationOptionsFactory();

        $service = $factory->createService($serviceManager);

        $this->assertInstanceOf('Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions', $service);
    }
}
