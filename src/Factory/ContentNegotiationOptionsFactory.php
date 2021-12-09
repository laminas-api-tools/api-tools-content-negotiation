<?php

declare(strict_types=1);

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;

use function is_array;

class ContentNegotiationOptionsFactory
{
    /**
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
     * @return array
     */
    private function getConfig(ContainerInterface $container)
    {
        if (! $container->has('config')) {
            return [];
        }

        $config = $container->get('config');

        if (
            ! isset($config['api-tools-content-negotiation'])
            || ! is_array($config['api-tools-content-negotiation'])
        ) {
            return [];
        }

        return $config['api-tools-content-negotiation'];
    }
}
