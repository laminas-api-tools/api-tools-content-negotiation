<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\ContentNegotiation\Request as HttpRequest;
use Laminas\Console\Request as ConsoleRequest;

class RequestFactory
{
    /**
     * @param  ContainerInterface $container
     * @return ConsoleRequest|HttpRequest
     */
    public function __invoke(ContainerInterface $container)
    {
        if (PHP_SAPI === 'cli') {
            return new ConsoleRequest();
        }

        return new HttpRequest();
    }
}
