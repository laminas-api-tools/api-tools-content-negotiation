<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ContentNegotiation\ContentTypeListener;
use Laminas\Http\Request;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase as TestCase;

class ContentTypeListenerTest extends TestCase
{
    public function setUp()
    {
        $this->listener = new ContentTypeListener();
    }

    public function methodsWithBodies()
    {
        return [
            'post' => array('POST'),
            'patch' => array('PATCH'),
            'put' => array('PUT'),
        ];
    }

    /**
     * @group 3
     * @dataProvider methodsWithBodies
     */
    public function testJsonDecodeErrorsRaiseExceptions($method)
    {
        $listener = $this->listener;

        $request = new Request();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $request->setContent('Invalid JSON data');

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch(new RouteMatch([]));

        $this->setExpectedException('DomainException', 'JSON decoding error', 400);
        $listener($event);
    }
}
