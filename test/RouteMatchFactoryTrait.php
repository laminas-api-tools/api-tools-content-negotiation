<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\Mvc\Router\RouteMatch as V2RouteMatch;
use Laminas\Router\RouteMatch;

trait RouteMatchFactoryTrait
{
    /**
     * Create and return a version-specific RouteMatch instance.
     *
     * @param array $params
     * @return V2RouteMatch|RouteMatch
     */
    public function createRouteMatch(array $params = [])
    {
        $class = class_exists(V2RouteMatch::class) ? V2RouteMatch::class : RouteMatch::class;
        return new $class($params);
    }
}
