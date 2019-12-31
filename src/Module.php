<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ContentNegotiation\AcceptFilterListener;
use Laminas\ApiTools\ContentNegotiation\AcceptListener;
use Laminas\ApiTools\ContentNegotiation\ContentTypeFilterListener;
use Laminas\ApiTools\ContentNegotiation\ContentTypeListener;
use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\DispatchableInterface;

class Module
{
    /**
     * Return module-specific configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Listen to bootstrap event.
     *
     * Attaches the ContentTypeListener, AcceptFilterListener, and
     * ContentTypeFilterListener to the application event manager.
     *
     * Attaches the AcceptListener as a shared listener for controller dispatch
     * events.
     *
     * @param MvcEvent $e
     * @return void
     */
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $services = $app->getServiceManager();
        $eventManager = $app->getEventManager();

        $eventManager->attach(MvcEvent::EVENT_ROUTE, $services->get(ContentTypeListener::class), -625);

        $services->get(AcceptFilterListener::class)->attach($eventManager);
        $services->get(ContentTypeFilterListener::class)->attach($eventManager);

        $contentNegotiationOptions = $services->get(ContentNegotiationOptions::class);
        if ($contentNegotiationOptions->getXHttpMethodOverrideEnabled()) {
            $services->get(HttpMethodOverrideListener::class)->attach($eventManager);
        }

        $sharedEventManager = $eventManager->getSharedManager();
        $sharedEventManager->attach(
            DispatchableInterface::class,
            MvcEvent::EVENT_DISPATCH,
            $services->get(AcceptListener::class),
            -10
        );
    }
}
