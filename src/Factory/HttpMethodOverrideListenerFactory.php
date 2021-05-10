<?php

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;
use Laminas\ApiTools\ContentNegotiation\HttpMethodOverrideListener;

class HttpMethodOverrideListenerFactory
{
    /**
     * @param  ContainerInterface $container
     * @return HttpMethodOverrideListener
     */
    public function __invoke(ContainerInterface $container)
    {
        $options = $container->get(ContentNegotiationOptions::class);
        $httpOverrideMethods = $options->getHttpOverrideMethods();
        $listener = new HttpMethodOverrideListener($httpOverrideMethods);

        return $listener;
    }
}
