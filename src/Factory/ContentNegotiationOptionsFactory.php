<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;

class ContentNegotiationOptionsFactory
{
    /**
     * @param  ContainerInterface $container
     * @return ContentNegotiationOptions
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ContentNegotiationOptions($this->getConfig($container));
    }

    /**
     * Attempt to retrieve the api-tools-content-negotiation configuration.
     *
     * - Consults the container's 'config' service, returning an empty array
     *   if not found.
     * - Validates that the api-tools-content-negotiation key exists, and evaluates
     *   to an array; if not,returns an empty array.
     *
     * @param ContainerInterface $container
     * @return array
     */
    private function getConfig(ContainerInterface $container)
    {
        if (! $container->has('config')) {
            return [];
        }

        $config = $container->get('config');

        if (! isset($config['api-tools-content-negotiation'])
            || ! is_array($config['api-tools-content-negotiation'])
        ) {
            return [];
        }

        return $config['api-tools-content-negotiation'];
    }
}
