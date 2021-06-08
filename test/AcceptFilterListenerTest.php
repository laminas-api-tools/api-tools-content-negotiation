<?php

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ContentNegotiation\AcceptFilterListener;
use Laminas\Http\Headers;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class AcceptFilterListenerTest extends TestCase
{
    protected function setUp()
    {
        $this->listener = new AcceptFilterListener();
    }

    /**
     * @group 58
     */
    public function testMissingAcceptHeaderIndicatesValidMediaType()
    {
        $headers = $this->prophesize(Headers::class);
        $headers->has('accept')->willReturn(false);

        $r = new ReflectionMethod($this->listener, 'validateMediaType');
        $r->setAccessible(true);

        $this->assertTrue($r->invoke($this->listener, 'application/json', $headers->reveal()));
    }
}
