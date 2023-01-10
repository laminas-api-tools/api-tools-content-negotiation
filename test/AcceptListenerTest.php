<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\ApiTools\ContentNegotiation\AcceptListener;
use Laminas\Http\Request;
use Laminas\Mvc\Controller\Plugin\AcceptableViewModelSelector;
use Laminas\Mvc\Controller\PluginManager as ControllerPluginManager;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\RequestInterface;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use LaminasTest\ApiTools\ContentNegotiation\TestAsset\ContentTypeController;
use PHPUnit\Framework\TestCase;

class AcceptListenerTest extends TestCase
{
    use RouteMatchFactoryTrait;

    /** @var AcceptListener */
    protected $listener;

    /** @var MvcEvent */
    protected $event;

    /** @var ContentTypeController */
    protected $controller;

    protected function setUp(): void
    {
        $plugins = new ControllerPluginManager(new ServiceManager());
        /** @var AcceptableViewModelSelector $selector */
        $selector = $plugins->get('AcceptableViewModelSelector');

        $this->listener   = new AcceptListener($selector, [
            'controllers' => [
                ContentTypeController::class => 'Json',
            ],
            'selectors'   => [
                'Json' => [
                    JsonModel::class => [
                        'application/json',
                        'application/*+json',
                    ],
                ],
            ],
        ]);
        $this->event      = new MvcEvent();
        $this->controller = new ContentTypeController();
        $this->event->setTarget($this->controller);
        $this->event->setRequest(new Request());
        $this->event->setRouteMatch($this->createRouteMatch([
            'controller' => __NAMESPACE__ . '\TestAsset\ContentTypeController',
        ]));

        $this->controller->setEvent($this->event);
        $this->controller->setRequest($this->event->getRequest());
        $this->controller->setPluginManager($plugins);
    }

    public function testInabilityToResolveViewModelReturnsApiProblemResponse(): void
    {
        $listener = $this->listener;
        $this->event->setResult(['foo' => 'bar']);

        $response = $listener($this->event);
        $this->assertInstanceOf(ApiProblemResponse::class, $response);
        $this->assertEquals(406, $response->getApiProblem()->status);
        $this->assertStringContainsString('Unable to resolve', $response->getApiProblem()->detail);
    }

    public function testReturnADefaultViewModelIfNoCriteriaSpecifiedForAController(): void
    {
        /** @var AcceptableViewModelSelector $selector */
        $selector = $this->controller->plugin('AcceptableViewModelSelector');
        $listener = new AcceptListener($selector, []);
        $this->event->setResult(['foo' => 'bar']);

        $listener($this->event);
        $result = $this->event->getResult();
        $this->assertInstanceOf(ModelInterface::class, $result);
    }

    /**
     * @group 22
     */
    public function testShouldExitEarlyIfNonHttpRequestPresentInEvent(): void
    {
        /** @var RequestInterface $request */
        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $this->event->setRequest($request);

        $listener = $this->listener;
        $this->event->setResult(['foo' => 'bar']);

        $this->assertNull($listener($this->event));
    }
}
