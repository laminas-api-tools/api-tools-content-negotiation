<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ContentNegotiation\Request;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

class RequestTest extends TestCase
{
    protected function setUp()
    {
        $this->request = new Request();
    }

    public function testIsAnHttpRequest()
    {
        $this->assertInstanceOf('Laminas\Http\Request', $this->request);
    }

    public function testIsAPhpEnvironmentHttpRequest()
    {
        $this->assertInstanceOf('Laminas\Http\PhpEnvironment\Request', $this->request);
    }

    public function testDefinesAGetContentAsStreamMethod()
    {
        $this->assertTrue(method_exists($this->request, 'getContentAsStream'));
    }

    public function testDefaultContentStreamIsPhpInputStream()
    {
        $this->assertAttributeEquals('php://input', 'contentStream', $this->request);
    }

    public function testCanSetStreamUriForContent()
    {
        $expected = 'file://' . realpath(__FILE__);
        $this->request->setContentStream($expected);
        $this->assertAttributeEquals($expected, 'contentStream', $this->request);
    }

    public function testGetContentAsStreamReturnsResource()
    {
        $this->request->setContentStream('file://' . realpath(__FILE__));
        $stream = $this->request->getContentAsStream();
        $this->assertInternalType('resource', $stream);
    }

    public function testReturnsPhpTemporaryStreamIfContentHasAlreadyBeenRetrieved()
    {
        $r = new ReflectionObject($this->request);
        $p = $r->getProperty('content');
        $p->setAccessible(true);
        $p->setValue($this->request, 'bam!');

        $stream = $this->request->getContentAsStream();
        $this->assertEquals('bam!', stream_get_contents($stream));
    }
}
