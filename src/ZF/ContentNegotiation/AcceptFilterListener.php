<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ApiProblem\Exception\DomainException;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\EventManager\SharedListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\ArrayUtils;

class AcceptFilterListener extends ContentTypeFilterListener
{
    /**
     * Test if the accept content-type received is allowable.
     * 
     * @param  MvcEvent $e 
     */
    public function onDispatch(MvcEvent $e)
    {
        if (empty($this->config)) {
            return;
        }

        $controllerName = $e->getRouteMatch()->getParam('controller');
        if (!isset($this->config[$controllerName])) {
            return;
        }

        $request = $e->getRequest();
        if (!method_exists($request, 'getHeaders')) {
            // Not an HTTP request; nothing to do
            return;
        }

        $headers = $request->getHeaders();

        $matched = false;
        if (is_string($this->config[$controllerName])) {
            $matched = $this->validateContentType($this->config[$controllerName], $headers);
        } elseif (is_array($this->config[$controllerName])) {
            foreach ($this->config[$controllerName] as $whitelistType) {
                $matched = $this->validateContentType($whitelistType, $headers);
                if ($matched) {
                    break;
                }
            }
        }

        if (!$matched) {
            throw new DomainException('Cannot honor Accept type specified', 406);
        }
    }

    protected function validateContentType($match, $headers)
    {
        if (!$headers->has('accept')) {
            return false;
        }

        $accept = $headers->get('accept');
        if ($accept->match($match)) {
            return true;
        }

        return false;
    }
}
