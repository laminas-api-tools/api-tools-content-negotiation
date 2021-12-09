<?php

declare(strict_types=1);

namespace Laminas\ApiTools\ContentNegotiation\ControllerPlugin;

use Laminas\ApiTools\ContentNegotiation\ParameterDataContainer;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

class QueryParams extends AbstractPlugin
{
    /**
     * @return array
     * @throws RuntimeException If controller does not implement InjectApplicationEventInterface.
     */
    public function __invoke()
    {
        $controller = $this->getController();
        if ($controller instanceof AbstractController) {
            $parameterData = $controller->getEvent()->getParam('LaminasContentNegotiationParameterData');
            if ($parameterData instanceof ParameterDataContainer) {
                return $parameterData->getQueryParams();
            }
        }

        return $this->getController()->getRequest()->getQuery()->toArray();
    }
}
