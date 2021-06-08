<?php

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ContentNegotiation\Request;
use PHPUnit\Framework\TestCase;
use ReflectionObject;
use ReflectionProperty;

use function method_exists;
use function realpath;
use function stream_get_contents;

class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        $this->request = new Request();
    }

    public function testIsAnHttpRequest(): void
    {
        $this->assertInstanceOf(\Laminas\Http\Request::class, $this->request);
    }

    public function testIsAPhpEnvironmentHttpRequest(): void
    {
        $this->assertInstanceOf(\Laminas\Http\PhpEnvironment\Request::class, $this->request);
    }

    public function testDefinesAGetContentAsStreamMethod(): void
    {
        $this->assertTrue(method_exists($this->request, 'getContentAsStream'));
    }

    public function testDefaultContentStreamIsPhpInputStream(): void
    {
        $property = $this->getContentStreamReflectionProperty();

        $this->assertSame('php://input', $property->getValue($this->request));
    }

    public function testCanSetStreamUriForContent(): void
    {
        $property = $this->getContentStreamReflectionProperty();

        $expected = 'file://' . realpath(__FILE__);
        $this->request->setContentStream($expected);

        $this->assertSame($expected, $property->getValue($this->request));
    }

    public function testGetContentAsStreamReturnsResource(): void
    {
        $this->request->setContentStream('file://' . realpath(__FILE__));
        $stream = $this->request->getContentAsStream();
        $this->assertIsResource($stream);
    }

    public function testReturnsPhpTemporaryStreamIfContentHasAlreadyBeenRetrieved(): void
    {
        $r = new ReflectionObject($this->request);
        $p = $r->getProperty('content');
        $p->setAccessible(true);
        $p->setValue($this->request, 'bam!');

        $stream = $this->request->getContentAsStream();
        $this->assertEquals('bam!', stream_get_contents($stream));
    }

    private function getContentStreamReflectionProperty(): ReflectionProperty
    {
        $property = new ReflectionProperty(Request::class, 'contentStream');
        $property->setAccessible(true);

        return $property;
    }
}
