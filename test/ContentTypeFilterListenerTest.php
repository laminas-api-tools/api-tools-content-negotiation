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
use PHPUnit_Framework_TestCase as TestCase;

class ContentTypeFilterListenerTest extends TestCase
{
    use RouteMatchFactoryTrait;

    public function setUp()
    {
        $this->listener   = new ContentTypeFilterListener();
        $this->event      = new MvcEvent();
        $this->event->setTarget(new TestAsset\ContentTypeController());
        $this->event->setRequest(new Request());
        $this->event->setRouteMatch($this->createRouteMatch([
            'controller' => __NAMESPACE__ . '\TestAsset\ContentTypeController',
        ]));
    }

    public function testListenerDoesNothingIfNoConfigurationExistsForController()
    {
        $this->assertNull($this->listener->onRoute($this->event));
    }

    public function testListenerDoesNothingIfRequestContentTypeIsInControllerWhitelist()
    {
        $contentType = 'application/vnd.laminas.v1.foo+json';
        $this->listener->setConfig([
            'LaminasTest\ApiTools\ContentNegotiation\TestAsset\ContentTypeController' => [
                $contentType,
            ],
        ]);
        $this->event->getRequest()->getHeaders()->addHeaderLine('content-type', $contentType);
        $this->assertNull($this->listener->onRoute($this->event));
    }

    public function testListenerReturnsApiProblemResponseIfRequestContentTypeIsNotInControllerWhitelist()
    {
        $contentType = 'application/vnd.laminas.v1.foo+json';
        $this->listener->setConfig([
            'LaminasTest\ApiTools\ContentNegotiation\TestAsset\ContentTypeController' => [
                'application/xml',
            ],
        ]);
        $request = $this->event->getRequest();
        $request->getHeaders()->addHeaderLine('content-type', $contentType);
        $request->setContent('<?xml version="1.0"?><foo><bar>baz</bar></foo>');

        $response = $this->listener->onRoute($this->event);
        $this->assertInstanceOf('Laminas\ApiTools\ApiProblem\ApiProblemResponse', $response);
        $this->assertContains('Invalid content-type', $response->getApiProblem()->detail);
    }


    /**
     * @group 66
     */
    public function testCastsObjectBodyContentToStringBeforeWorkingWithIt()
    {
        $contentType = 'application/vnd.laminas.v1.foo+json';
        $this->listener->setConfig([
            'LaminasTest\ApiTools\ContentNegotiation\TestAsset\ContentTypeController' => [
                $contentType,
            ],
        ]);
        $request = $this->event->getRequest();

        $request->getHeaders()->addHeaderLine('content-type', $contentType);
        $request->setContent(new TestAsset\BodyContent());

        $this->assertNull($this->listener->onRoute($this->event));
    }
}
