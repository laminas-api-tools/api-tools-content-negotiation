<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ContentNegotiation\AcceptListener;
use Laminas\EventManager\SharedEventManager;
use Laminas\Http\Request;
use Laminas\Mvc\Controller\PluginManager as ControllerPluginManager;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase as TestCase;

class AcceptListenerTest extends TestCase
{
    public function setUp()
    {
        $plugins  = new ControllerPluginManager();
        $selector = $plugins->get('AcceptableViewModelSelector');

        $this->listener   = new AcceptListener($selector, array(
            'controllers' => array(
                'LaminasTest\ApiTools\ContentNegotiation\TestAsset\ContentTypeController' => 'Json',
            ),
            'selectors' => array(
                'Json' => array(
                    'Laminas\View\Model\JsonModel' => array(
                        'application/json',
                        'application/*+json',
                    ),
                ),
            ),
        ));
        $this->event      = new MvcEvent();
        $this->controller = new TestAsset\ContentTypeController();
        $this->event->setTarget($this->controller);
        $this->event->setRequest(new Request);
        $this->event->setRouteMatch(new RouteMatch(array(
            'controller' => __NAMESPACE__ . '\TestAsset\ContentTypeController',
        )));

        $this->controller->setEvent($this->event);
        $this->controller->setRequest($this->event->getRequest());
        $this->controller->setPluginManager($plugins);
    }

    public function testInablityToResolveViewModelRaisesApiProblemDomainException()
    {
        $listener = $this->listener;
        $this->event->setResult(array('foo' => 'bar'));

        $this->setExpectedException('Laminas\ApiTools\ApiProblem\Exception\DomainException', 'Unable to resolve', 406);
        $listener($this->event);
    }

    public function testReturnADefaultViewModelIfNoCriteriaSpecifiedForAController()
    {
        $selector = $this->controller->plugin('AcceptableViewModelSelector');
        $listener = new AcceptListener($selector, array());
        $this->event->setResult(array('foo' => 'bar'));

        $listener($this->event);
        $result = $this->event->getResult();
        $this->assertInstanceOf('Laminas\View\Model\ModelInterface', $result);
    }
}
