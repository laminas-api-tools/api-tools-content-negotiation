<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation;

use Laminas\Mvc\Controller\Plugin\AcceptableViewModelSelector;
use Laminas\Mvc\MvcEvent;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Laminas\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array('factories' => array(
            'Laminas\ApiTools\ContentNegotiation\AcceptListener' => function ($services) {
                $config = array();
                if ($services->has('Config')) {
                    $appConfig = $services->get('Config');
                    if (isset($appConfig['api-tools-content-negotiation'])
                        && is_array($appConfig['api-tools-content-negotiation'])
                    ) {
                        $config = $appConfig['api-tools-content-negotiation'];
                    }
                }

                $selector = null;
                if ($services->has('ControllerPluginManager')) {
                    $plugins = $services->get('ControllerPluginManager');
                    if ($plugins->has('AcceptableViewModelSelector')) {
                        $selector = $plugins->get('AcceptableViewModelSelector');
                    }
                }
                if (null === $selector) {
                    $selector = new AcceptableViewModelSelector();
                }
                return new AcceptListener($selector, $config);
            },
            'Laminas\ApiTools\ContentNegotiation\AcceptFilterListener' => function ($services) {
                $listener = new AcceptFilterListener();

                $config   = array();
                if ($services->has('Config')) {
                    $moduleConfig = false;
                    $appConfig    = $services->get('Config');
                    if (isset($appConfig['api-tools-content-negotiation'])
                        && is_array($appConfig['api-tools-content-negotiation'])
                    ) {
                        $moduleConfig = $appConfig['api-tools-content-negotiation'];
                    }

                    if ($moduleConfig
                        && isset($moduleConfig['accept-whitelist'])
                        && is_array($moduleConfig['accept-whitelist'])
                    ) {
                        $config = $moduleConfig['accept-whitelist'];
                    }
                }

                if (!empty($config)) {
                    $listener->setConfig($config);
                }

                return $listener;
            },
            'Laminas\ApiTools\ContentNegotiation\ContentTypeFilterListener' => function ($services) {
                $listener = new ContentTypeFilterListener();

                $config   = array();
                if ($services->has('Config')) {
                    $moduleConfig = false;
                    $appConfig    = $services->get('Config');
                    if (isset($appConfig['api-tools-content-negotiation'])
                        && is_array($appConfig['api-tools-content-negotiation'])
                    ) {
                        $moduleConfig = $appConfig['api-tools-content-negotiation'];
                    }

                    if ($moduleConfig
                        && isset($moduleConfig['content-type-whitelist'])
                        && is_array($moduleConfig['content-type-whitelist'])
                    ) {
                        $config = $moduleConfig['content-type-whitelist'];
                    }
                }

                if (!empty($config)) {
                    $listener->setConfig($config);
                }

                return $listener;
            },
        ));
    }

    public function onBootstrap($e)
    {
        $app      = $e->getApplication();
        $services = $app->getServiceManager();
        $em       = $app->getEventManager();

        $em->attach(MvcEvent::EVENT_ROUTE, new ContentTypeListener(), -99);

        $sem = $em->getSharedManager();
        $sem->attach(
            'Laminas\Stdlib\DispatchableInterface',
            MvcEvent::EVENT_DISPATCH,
            $services->get('Laminas\ApiTools\ContentNegotiation\AcceptListener'),
            -10
        );
        $sem->attachAggregate($services->get('Laminas\ApiTools\ContentNegotiation\AcceptFilterListener'));
        $sem->attachAggregate($services->get('Laminas\ApiTools\ContentNegotiation\ContentTypeFilterListener'));
    }
}
