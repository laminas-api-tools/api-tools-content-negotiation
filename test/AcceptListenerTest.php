<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ContentNegotiation\AcceptListener;
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

        $this->listener   = new AcceptListener($selector, [
            'controllers' => [
                'LaminasTest\ApiTools\ContentNegotiation\TestAsset\ContentTypeController' => 'Json',
            ],
            'selectors' => [
                'Json' => [
                    'Laminas\View\Model\JsonModel' => [
                        'application/json',
                        'application/*+json',
                    ],
                ],
            ],
        ]);
        $this->event      = new MvcEvent();
        $this->controller = new TestAsset\ContentTypeController();
        $this->event->setTarget($this->controller);
        $this->event->setRequest(new Request);
        $this->event->setRouteMatch(new RouteMatch([
            'controller' => __NAMESPACE__ . '\TestAsset\ContentTypeController',
        ]));

        $this->controller->setEvent($this->event);
        $this->controller->setRequest($this->event->getRequest());
        $this->controller->setPluginManager($plugins);
    }

    public function testInabilityToResolveViewModelReturnsApiProblemResponse()
    {
        $listener = $this->listener;
        $this->event->setResult(['foo' => 'bar']);

        $response = $listener($this->event);
        $this->assertInstanceOf('Laminas\ApiTools\ApiProblem\ApiProblemResponse', $response);
        $this->assertEquals(406, $response->getApiProblem()->status);
        $this->assertContains('Unable to resolve', $response->getApiProblem()->detail);
    }

    public function testReturnADefaultViewModelIfNoCriteriaSpecifiedForAController()
    {
        $selector = $this->controller->plugin('AcceptableViewModelSelector');
        $listener = new AcceptListener($selector, []);
        $this->event->setResult(['foo' => 'bar']);

        $listener($this->event);
        $result = $this->event->getResult();
        $this->assertInstanceOf('Laminas\View\Model\ModelInterface', $result);
    }

    /**
     * @group 22
     */
    public function testShouldExitEarlyIfNonHttpRequestPresentInEvent()
    {
        $request = $this->getMock('Laminas\Stdlib\RequestInterface');
        $this->event->setRequest($request);

        $listener = $this->listener;
        $this->event->setResult(['foo' => 'bar']);

        $this->assertNull($listener($this->event));
    }
}
