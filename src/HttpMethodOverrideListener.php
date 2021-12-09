<?php

declare(strict_types=1);

namespace Laminas\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\MvcEvent;

use function array_key_exists;
use function in_array;
use function sprintf;

class HttpMethodOverrideListener extends AbstractListenerAggregate
{
    /** @var array */
    protected $httpMethodOverride = [];

    /**
     * @param array $httpMethodOverride
     */
    public function __construct(array $httpMethodOverride)
    {
        $this->httpMethodOverride = $httpMethodOverride;
    }

    /**
     * Priority is set very high (should be executed before all other listeners that rely on the request method value).
     * TODO: Check priority value, maybe value should be even higher??
     *
     * @param int                   $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], -40);
    }

    /**
     * Checks for X-HTTP-Method-Override header and sets header inside request object.
     *
     * @return void|ApiProblemResponse
     */
    public function onRoute(MvcEvent $event)
    {
        $request = $event->getRequest();

        if (! $request instanceof HttpRequest) {
            return;
        }

        if (! $request->getHeaders()->has('X-HTTP-Method-Override')) {
            return;
        }

        $method = $request->getMethod();

        if (! array_key_exists($method, $this->httpMethodOverride)) {
            return new ApiProblemResponse(new ApiProblem(
                400,
                sprintf('Overriding %s method with X-HTTP-Method-Override header is not allowed', $method)
            ));
        }

        $header         = $request->getHeader('X-HTTP-Method-Override');
        $overrideMethod = $header->getFieldValue();
        $allowedMethods = $this->httpMethodOverride[$method];

        if (! in_array($overrideMethod, $allowedMethods)) {
            return new ApiProblemResponse(new ApiProblem(
                400,
                sprintf('Illegal override method %s in X-HTTP-Method-Override header', $overrideMethod)
            ));
        }

        $request->setMethod($overrideMethod);
    }
}
