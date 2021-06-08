<?php

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\ApiTools\ContentNegotiation\ContentTypeFilterListener;
use Laminas\Http\Request;
use Laminas\Mvc\MvcEvent;
use LaminasTest\ApiTools\ContentNegotiation\TestAsset\ContentTypeController;
use PHPUnit\Framework\TestCase;

class ContentTypeFilterListenerTest extends TestCase
{
    use RouteMatchFactoryTrait;

    protected function setUp()
    {
        $this->listener = new ContentTypeFilterListener();
        $this->event    = new MvcEvent();
        $this->event->setTarget(new ContentTypeController());
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
            ContentTypeController::class => [
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
            ContentTypeController::class => [
                'application/xml',
            ],
        ]);
        $request = $this->event->getRequest();
        $request->getHeaders()->addHeaderLine('content-type', $contentType);
        $request->setContent('<?xml version="1.0"?><foo><bar>baz</bar></foo>');

        $response = $this->listener->onRoute($this->event);
        $this->assertInstanceOf(ApiProblemResponse::class, $response);
        $this->assertContains('Invalid content-type', $response->getApiProblem()->detail);
    }

    /**
     * @group 66
     */
    public function testCastsObjectBodyContentToStringBeforeWorkingWithIt()
    {
        $contentType = 'application/vnd.laminas.v1.foo+json';
        $this->listener->setConfig([
            ContentTypeController::class => [
                $contentType,
            ],
        ]);
        $request = $this->event->getRequest();

        $request->getHeaders()->addHeaderLine('content-type', $contentType);
        $request->setContent(new TestAsset\BodyContent());

        $this->assertNull($this->listener->onRoute($this->event));
    }
}
