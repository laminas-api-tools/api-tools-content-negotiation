<?php

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\ApiTools\ContentNegotiation\HttpMethodOverrideListener;
use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\MvcEvent;
use PHPUnit\Framework\TestCase;

use function sprintf;

class HttpMethodOverrideListenerTest extends TestCase
{
    use RouteMatchFactoryTrait;

    /** @var HttpMethodOverrideListener */
    protected $listener;

    /** @var array<HttpRequest::METHOD_*, array<array-key, HttpRequest::METHOD_*>> */
    protected $httpMethodOverride = [
        HttpRequest::METHOD_GET  => [
            HttpRequest::METHOD_HEAD,
            HttpRequest::METHOD_POST,
            HttpRequest::METHOD_PUT,
            HttpRequest::METHOD_DELETE,
            HttpRequest::METHOD_PATCH,
        ],
        HttpRequest::METHOD_POST => [],
    ];

    /**
     * Set up test
     */
    protected function setUp(): void
    {
        $this->listener = new HttpMethodOverrideListener($this->httpMethodOverride);
    }

    /** @psalm-return array<string, array{0: HttpRequest::METHOD_*}> */
    public function httpMethods(): array
    {
        return [
            'head'   => [HttpRequest::METHOD_HEAD],
            'post'   => [HttpRequest::METHOD_POST],
            'put'    => [HttpRequest::METHOD_PUT],
            'delete' => [HttpRequest::METHOD_DELETE],
            'patch'  => [HttpRequest::METHOD_PATCH],
        ];
    }

    /**
     * @dataProvider httpMethods
     */
    public function testHttpMethodOverrideListener(string $method)
    {
        $listener = $this->listener;

        $request = new HttpRequest();
        $request->setMethod('GET');
        $request->getHeaders()->addHeaderLine('X-HTTP-Method-Override', $method);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $result = $listener->onRoute($event);
        $this->assertEquals($method, $request->getMethod());
    }

    /**
     * @dataProvider httpMethods
     */
    public function testHttpMethodOverrideListenerReturnsProblemResponseForMethodNotInConfig(string $method)
    {
        $listener = $this->listener;

        $request = new HttpRequest();
        $request->setMethod('PATCH');
        $request->getHeaders()->addHeaderLine('X-HTTP-Method-Override', $method);

        $event = new MvcEvent();
        $event->setRequest($request);

        $result = $listener->onRoute($event);
        $this->assertInstanceOf(ApiProblemResponse::class, $result);
        $problem = $result->getApiProblem();
        $this->assertEquals(400, $problem->status);
        $this->assertStringContainsString(
            'Overriding PATCH method with X-HTTP-Method-Override header is not allowed',
            $problem->detail
        );
    }

    /**
     * @dataProvider httpMethods
     */
    public function testHttpMethodOverrideListenerReturnsProblemResponseForIllegalOverrideValue(string $method)
    {
        $listener = $this->listener;

        $request = new HttpRequest();
        $request->setMethod('POST');
        $request->getHeaders()->addHeaderLine('X-HTTP-Method-Override', $method);

        $event = new MvcEvent();
        $event->setRequest($request);

        $result = $listener->onRoute($event);
        $this->assertInstanceOf(ApiProblemResponse::class, $result);
        $problem = $result->getApiProblem();
        $this->assertEquals(400, $problem->status);
        $this->assertStringContainsString(
            sprintf('Illegal override method %s in X-HTTP-Method-Override header', $method),
            $problem->detail
        );
    }
}
