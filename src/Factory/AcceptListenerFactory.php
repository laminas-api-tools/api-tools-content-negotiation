<?php

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\ContentNegotiation\AcceptListener;
use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;
use Laminas\Mvc\Controller\Plugin\AcceptableViewModelSelector;

class AcceptListenerFactory
{
    /**
     * @param  ContainerInterface $container
     * @return AcceptListener
     */
    public function __invoke(ContainerInterface $container)
    {
        return new AcceptListener(
            $this->getAcceptableViewModelSelector($container),
            $container->get(ContentNegotiationOptions::class)->toArray()
        );
    }

    /**
     * Retrieve or generate the AcceptableViewModelSelector plugin instance.
     *
     * @param  ContainerInterface $container
     * @return AcceptableViewModelSelector
     */
    private function getAcceptableViewModelSelector(ContainerInterface $container)
    {
        if (! $container->has('ControllerPluginManager')) {
            return new AcceptableViewModelSelector();
        }

        $plugins = $container->get('ControllerPluginManager');
        if (! $plugins->has('AcceptableViewModelSelector')) {
            return new AcceptableViewModelSelector();
        }

        return $plugins->get('AcceptableViewModelSelector');
    }
}
