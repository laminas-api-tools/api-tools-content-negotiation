<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Laminas\ApiTools\ContentNegotiation\AcceptFilterListener;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class AcceptFilterListenerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $listener = new AcceptFilterListener();

        /* @var $options \Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions */
        $options = $serviceLocator->get('Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions');

        $listener->setConfig($options->getAcceptWhitelist());

        return $listener;
    }
}
