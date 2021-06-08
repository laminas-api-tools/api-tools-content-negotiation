<?php

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\ContentNegotiation\Request as HttpRequest;
use Laminas\Console\Console;
use Laminas\Console\Request as ConsoleRequest;

use function class_exists;

use const PHP_SAPI;

class RequestFactory
{
    /**
     * @return ConsoleRequest|HttpRequest
     */
    public function __invoke(ContainerInterface $container)
    {
        // If console tooling is present, use that to determine whether or not
        // we are in a console environment. This approach allows overriding the
        // environment for purposes of testing HTTP requests from the CLI.
        if (class_exists(Console::class)) {
            return Console::isConsole() ? new ConsoleRequest() : new HttpRequest();
        }

        // If console tooling is not present, we use the PHP_SAPI value to decide.
        return PHP_SAPI === 'cli' ? new ConsoleRequest() : new HttpRequest();
    }
}
