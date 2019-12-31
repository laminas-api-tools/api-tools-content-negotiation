<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;
use Laminas\ApiTools\ContentNegotiation\Factory\HttpMethodOverrideListenerFactory;
use Laminas\ApiTools\ContentNegotiation\HttpMethodOverrideListener;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class HttpMethodOverrideListenerFactoryTest extends TestCase
{
    public function testCreateServiceShouldReturnContentTypeFilterListenerInstance()
    {
        /** @var ContentNegotiationOptions|ObjectProphecy $options */
        $options = $this->prophesize(ContentNegotiationOptions::class);
        $options->getHttpOverrideMethods()->willReturn([]);

        /** @var ContainerInterface|ObjectProphecy $container */
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(ContentNegotiationOptions::class)->willReturn($options);

        $factory = new HttpMethodOverrideListenerFactory();
        $service = $factory($container->reveal(), HttpMethodOverrideListener::class);

        $this->assertInstanceOf(HttpMethodOverrideListener::class, $service);
    }
}
