<?php

declare(strict_types=1);

namespace Laminas\ApiTools\ContentNegotiation\ControllerPlugin;

use Laminas\ApiTools\ContentNegotiation\ParameterDataContainer;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Mvc\Exception\RuntimeException;
use Laminas\Mvc\InjectApplicationEventInterface;

class RouteParams extends AbstractPlugin
{
    /**
     * @return array
     * @throws RuntimeException If controller does not implement InjectApplicationEventInterface.
     */
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
