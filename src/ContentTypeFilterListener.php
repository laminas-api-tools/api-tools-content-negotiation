<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\ArrayUtils;

class ContentTypeFilterListener implements ListenerAggregateInterface
{
    /**
     * Whitelist configuration
     * @var array
     */
    protected $config = array();

    /**
     * @var \Laminas\Stdlib\CallbackHandler
     */
    protected $listeners = array();

    /**
     * @param  EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -625);
    }

    /**
     * Detach listeners
     *
     * @param  EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Set whitelist configuration
     *
     * @param  array $config
     * @return self
     */
    public function setConfig(array $config)
    {
        $this->config = ArrayUtils::merge($this->config, $config);
        return $this;
    }

    /**
     * Test if the content-type received is allowable.
     *
     * @param  MvcEvent $e
     * @return null|ApiProblemResponse
     */
    public function onRoute(MvcEvent $e)
    {
        if (empty($this->config)) {
            return;
        }

        $controllerName = $e->getRouteMatch()->getParam('controller');
        if (!isset($this->config[$controllerName])) {
            return;
        }

        // Only worry about content types on HTTP methods that submit content
        // via the request body.
        $request = $e->getRequest();
        if (!method_exists($request, 'getHeaders')) {
            // Not an HTTP request; nothing to do
            return;
        }

        $requestBody = $request->getContent();
        if (empty($requestBody)) {
            return;
        }

        $headers           = $request->getHeaders();
        $contentTypeHeader = false;
        if (!$headers->has('content-type')) {
            return new ApiProblemResponse(new ApiProblem(415, 'Invalid content-type specified'));
        }

        $contentTypeHeader = $headers->get('content-type');

        $matched = $contentTypeHeader->match($this->config[$controllerName]);

        if (false === $matched) {
            return new ApiProblemResponse(new ApiProblem(415, 'Invalid content-type specified'));
        }
    }
}
