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

class BodyParam extends AbstractPlugin
{
    /**
     * Grabs a param from body match after content-negotiation
     *
     * @param  null|string $param
     * @param  null|mixed $default
     * @return mixed
     */
    public function __invoke($param = null, $default = null)
    {
        $controller = $this->getController();
        if ($controller instanceof AbstractController) {
            $parameterData = $controller->getEvent()->getParam('LaminasContentNegotiationParameterData');
            if ($parameterData instanceof ParameterDataContainer) {
                return $parameterData->getBodyParam($param, $default);
            }
        }

        return $controller->getRequest()->getPost($param, $default);
    }
}
