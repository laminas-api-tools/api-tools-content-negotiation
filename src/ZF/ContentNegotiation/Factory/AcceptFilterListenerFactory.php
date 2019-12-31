<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Laminas\ApiTools\ContentNegotiation\AcceptFilterListener;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class AcceptFilterListenerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $listener = new AcceptFilterListener();
        $config   = array();

        if ($serviceLocator->has('Config')) {
            $moduleConfig = false;
            $appConfig    = $serviceLocator->get('Config');
            if (isset($appConfig['api-tools-content-negotiation'])
                && is_array($appConfig['api-tools-content-negotiation'])
            ) {
                $moduleConfig = $appConfig['api-tools-content-negotiation'];
            }

            if ($moduleConfig
                && isset($moduleConfig['accept_whitelist'])
                && is_array($moduleConfig['accept_whitelist'])
            ) {
                $config = $moduleConfig['accept_whitelist'];
            }
        }

        if (!empty($config)) {
            $listener->setConfig($config);
        }

        return $listener;
    }
}
