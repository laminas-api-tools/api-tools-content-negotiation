<?php

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\ContentNegotiation\AcceptFilterListener;
use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;

class AcceptFilterListenerFactory
{
    /**
     * @param  ContainerInterface $container
     * @return AcceptFilterListener
     */
    public function __invoke(ContainerInterface $container)
    {
        $listener = new AcceptFilterListener();

        $options = $container->get(ContentNegotiationOptions::class);
        $listener->setConfig($options->getAcceptWhitelist());

        return $listener;
    }
}
