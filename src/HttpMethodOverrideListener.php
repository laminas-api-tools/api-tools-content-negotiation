<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\MvcEvent;

class HttpMethodOverrideListener extends AbstractListenerAggregate
{
    /**
     * @var array
     */
    protected $httpMethodOverride = [];

    /**
     * HttpMethodOverrideListener constructor.
     *
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
     * @param EventManagerInterface $events
     * @param int                   $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], -40);
    }

    /**
     * Checks for X-HTTP-Method-Override header and sets header inside request object.
     *
     * @param  MvcEvent $event
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

        $header = $request->getHeader('X-HTTP-Method-Override');
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
