<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ContentNegotiation\AcceptFilterListener;
use Laminas\Http\Headers;
use PHPUnit_Framework_TestCase as TestCase;
use ReflectionMethod;

class AcceptFilterListenerTest extends TestCase
{
    public function setUp()
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
