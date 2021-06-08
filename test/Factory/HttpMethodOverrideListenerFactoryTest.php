<?php

namespace LaminasTest\ApiTools\ContentNegotiation\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;
use Laminas\ApiTools\ContentNegotiation\Factory\HttpMethodOverrideListenerFactory;
use Laminas\ApiTools\ContentNegotiation\HttpMethodOverrideListener;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class HttpMethodOverrideListenerFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateServiceShouldReturnContentTypeFilterListenerInstance(): void
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
