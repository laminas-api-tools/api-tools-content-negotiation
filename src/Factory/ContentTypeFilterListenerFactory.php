<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;
use Laminas\ApiTools\ContentNegotiation\ContentTypeFilterListener;

class ContentTypeFilterListenerFactory
{
    /**
     * @param  ContainerInterface $container
     * @return ContentTypeFilterListener
     */
    public function __invoke(ContainerInterface $container)
    {
        $listener = new ContentTypeFilterListener();

        $options = $container->get(ContentNegotiationOptions::class);
        $listener->setConfig($options->getContentTypeWhitelist());

        return $listener;
    }
}
