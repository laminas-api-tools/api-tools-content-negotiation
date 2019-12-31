<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Laminas\ApiTools\ContentNegotiation\Request as HttpRequest;
use Laminas\Console\Console;
use Laminas\Console\Request as ConsoleRequest;

class RequestFactory
{
    /**
     * Create and return a request instance, according to current environment.
     *
     * @param  \Laminas\ServiceManager\ServiceLocatorInterface $services
     * @return ConsoleRequest|HttpRequest
     */
    public function __invoke($services)
    {
        if (Console::isConsole()) {
            return new ConsoleRequest();
        }

        return new HttpRequest();
    }
}
