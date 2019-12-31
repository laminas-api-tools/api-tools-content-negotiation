<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation;

use Laminas\Mvc\MvcEvent;

class Module
{
    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Laminas\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/',
                ),
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function onBootstrap($e)
    {
        $app      = $e->getApplication();
        $services = $app->getServiceManager();
        $em       = $app->getEventManager();

        $em->attach(MvcEvent::EVENT_ROUTE, new ContentTypeListener(), -625);
        $em->attachAggregate($services->get('Laminas\ApiTools\ContentNegotiation\AcceptFilterListener'));
        $em->attachAggregate($services->get('Laminas\ApiTools\ContentNegotiation\ContentTypeFilterListener'));

        $sem = $em->getSharedManager();
        $sem->attach(
            'Laminas\Stdlib\DispatchableInterface',
            MvcEvent::EVENT_DISPATCH,
            $services->get('Laminas\ApiTools\ContentNegotiation\AcceptListener'),
            -10
        );
    }
}
