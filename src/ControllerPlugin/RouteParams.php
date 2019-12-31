<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation\ControllerPlugin;

use Laminas\ApiTools\ContentNegotiation\ParameterDataContainer;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Mvc\Exception\RuntimeException;
use Laminas\Mvc\InjectApplicationEventInterface;

class RouteParams extends AbstractPlugin
{
    public function __invoke()
    {
        $controller = $this->getController();

        if (! $controller instanceof InjectApplicationEventInterface) {
            throw new RuntimeException(
                'Controllers must implement Laminas\Mvc\InjectApplicationEventInterface to use this plugin.'
            );
        }

        if ($controller instanceof AbstractController) {
            $parameterData = $controller->getEvent()->getParam('LaminasContentNegotiationParameterData');
            if ($parameterData instanceof ParameterDataContainer) {
                return $parameterData->getRouteParams();
            }
        }

        return $controller->getEvent()->getRouteMatch()->getParams();
    }
}
