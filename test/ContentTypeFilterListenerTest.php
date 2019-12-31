<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ContentNegotiation\ContentTypeFilterListener;
use Laminas\EventManager\EventManager;
use Laminas\Http\Request;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase as TestCase;

class ContentTypeFilterListenerTest extends TestCase
{
    public function setUp()
    {
        $this->listener   = new ContentTypeFilterListener();
        $this->event      = new MvcEvent();
        $this->event->setTarget(new TestAsset\ContentTypeController());
        $this->event->setRequest(new Request());
        $this->event->setRouteMatch(new RouteMatch(array(
            'controller' => __NAMESPACE__ . '\TestAsset\ContentTypeController',
        )));
    }

    public function testListenerDoesNothingIfNoConfigurationExistsForController()
    {
        $this->assertNull($this->listener->onRoute($this->event));
    }

    public function testListenerDoesNothingIfRequestContentTypeIsInControllerWhitelist()
    {
        $contentType = 'application/vnd.laminas.v1.foo+json';
        $this->listener->setConfig(array(
            'LaminasTest\ApiTools\ContentNegotiation\TestAsset\ContentTypeController' => array(
                $contentType,
            ),
        ));
        $this->event->getRequest()->getHeaders()->addHeaderLine('content-type', $contentType);
        $this->assertNull($this->listener->onRoute($this->event));
    }

    public function testListenerReturnsApiProblemResponseIfRequestContentTypeIsNotInControllerWhitelist()
    {
        $contentType = 'application/vnd.laminas.v1.foo+json';
        $this->listener->setConfig(array(
            'LaminasTest\ApiTools\ContentNegotiation\TestAsset\ContentTypeController' => array(
                'application/xml',
            ),
        ));
        $request = $this->event->getRequest();
        $request->getHeaders()->addHeaderLine('content-type', $contentType);
        $request->setContent('<?xml version="1.0"?><foo><bar>baz</bar></foo>');

        $response = $this->listener->onRoute($this->event);
        $this->assertInstanceOf('Laminas\ApiTools\ApiProblem\ApiProblemResponse', $response);
        $this->assertContains('Invalid content-type', $response->getApiProblem()->detail);
    }

    public function testAttachesToDispatchEventAtHighPriority()
    {
        $events = new EventManager();
        $this->listener->attach($events);
        $listeners = $events->getListeners('route');
        $this->assertEquals(1, count($listeners));
        $this->assertTrue($listeners->hasPriority(-625));
        $callback = $listeners->getIterator()->current()->getCallback();
        $this->assertEquals(array($this->listener, 'onRoute'), $callback);
    }
}
