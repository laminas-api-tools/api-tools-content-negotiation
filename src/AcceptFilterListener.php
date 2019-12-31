<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\Http\Headers as HttpHeaders;
use Laminas\Mvc\MvcEvent;

class AcceptFilterListener extends ContentTypeFilterListener
{
    /**
     * Test if the accept content-type received is allowable.
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
        if (! isset($this->config[$controllerName])) {
            return;
        }

        $request = $e->getRequest();
        if (! method_exists($request, 'getHeaders')) {
            // Not an HTTP request; nothing to do
            return;
        }

        $headers = $request->getHeaders();

        $matched = false;
        if (is_string($this->config[$controllerName])) {
            $matched = $this->validateMediaType($this->config[$controllerName], $headers);
        } elseif (is_array($this->config[$controllerName])) {
            foreach ($this->config[$controllerName] as $whitelistType) {
                $matched = $this->validateMediaType($whitelistType, $headers);
                if ($matched) {
                    break;
                }
            }
        }

        if (! $matched) {
            return new ApiProblemResponse(
                new ApiProblem(406, 'Cannot honor Accept type specified')
            );
        }
    }

    /**
     * Validate the passed mediatype against the appropriate header
     *
     * @param  string $match
     * @param  HttpHeaders $headers
     * @return bool
     */
    protected function validateMediaType($match, HttpHeaders $headers)
    {
        if (! $headers->has('accept')) {
            return true;
        }

        $accept = $headers->get('accept');
        if ($accept->match($match)) {
            return true;
        }

        return false;
    }
}
