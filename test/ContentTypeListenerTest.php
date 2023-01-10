<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\ApiTools\ContentNegotiation\ContentTypeListener;
use Laminas\ApiTools\ContentNegotiation\MultipartContentParser;
use Laminas\ApiTools\ContentNegotiation\ParameterDataContainer;
use Laminas\ApiTools\ContentNegotiation\Request as ContentNegotiationRequest;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\Request;
use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\Parameters;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

use function basename;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function filesize;
use function json_encode;
use function mkdir;
use function realpath;
use function rmdir;
use function sprintf;
use function str_replace;
use function strpos;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

use const PHP_OS;
use const UPLOAD_ERR_OK;

class ContentTypeListenerTest extends TestCase
{
    use RouteMatchFactoryTrait;

    /** @var ContentTypeListener */
    protected $listener;

    protected function setUp(): void
    {
        $this->listener = new ContentTypeListener();
    }

    /** @psalm-return array<string, array{0: string}> */
    public function methodsWithBodies(): array
    {
        return [
            'post'   => ['POST'],
            'patch'  => ['PATCH'],
            'put'    => ['PUT'],
            'delete' => ['DELETE'],
        ];
    }

    /**
     * @group 3
     * @dataProvider methodsWithBodies
     */
    public function testJsonDecodeErrorsReturnsProblemResponse(string $method): void
    {
        $listener = $this->listener;

        $request = new Request();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $request->setContent('Invalid JSON data');

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $result = $listener($event);
        $this->assertInstanceOf(ApiProblemResponse::class, $result);
        $problem = $result->getApiProblem();
        $this->assertEquals(400, $problem->status);
        $this->assertStringContainsString('JSON decoding', $problem->detail);
    }

    /**
     * @group 3
     * @dataProvider methodsWithBodies
     */
    public function testJsonDecodeStringErrorsReturnsProblemResponse(string $method): void
    {
        $listener = $this->listener;

        $request = new Request();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $request->setContent('"1"');

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $result = $listener($event);
        $this->assertInstanceOf(ApiProblemResponse::class, $result);
        $problem = $result->getApiProblem();
        $this->assertEquals(400, $problem->status);
        $this->assertStringContainsString('JSON decoding', $problem->detail);
    }

    /** @psalm-param array<string, array{0: string}> */
    public function multipartFormDataMethods(): array
    {
        return [
            'patch'  => ['patch'],
            'put'    => ['put'],
            'delete' => ['delete'],
        ];
    }

    /**
     * @dataProvider multipartFormDataMethods
     */
    public function testCanDecodeMultipartFormDataRequestsForPutPatchAndDeleteOperations(string $method): void
    {
        $request = new Request();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine(
            'Content-Type',
            'multipart/form-data; boundary=6603ddd555b044dc9a022f3ad9281c20'
        );
        $request->setContent(file_get_contents(__DIR__ . '/TestAsset/multipart-form-data.txt'));

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $listener = $this->listener;
        $listener($event);

        $parameterData = $event->getParam('LaminasContentNegotiationParameterData');
        $params        = $parameterData->getBodyParams();
        $this->assertEquals([
            'mime_type' => 'md',
        ], $params);

        $files = $request->getFiles();
        $this->assertEquals(1, $files->count());
        $file = $files->get('text');
        $this->assertIsArray($file);
        $this->assertArrayHasKey('error', $file);
        $this->assertArrayHasKey('name', $file);
        $this->assertArrayHasKey('tmp_name', $file);
        $this->assertArrayHasKey('size', $file);
        $this->assertArrayHasKey('type', $file);
        $this->assertEquals('README.md', $file['name']);
        $this->assertMatchesRegularExpression('/^laminasc/', basename($file['tmp_name']));
        $this->assertTrue(file_exists($file['tmp_name']));
    }

    /**
     * @dataProvider multipartFormDataMethods
     */
    public function testCanDecodeMultipartFormDataRequestsFromStreamsForPutAndPatchOperations(string $method): void
    {
        $request = new ContentNegotiationRequest();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine(
            'Content-Type',
            'multipart/form-data; boundary=6603ddd555b044dc9a022f3ad9281c20'
        );
        $request->setContentStream('file://' . realpath(__DIR__ . '/TestAsset/multipart-form-data.txt'));

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $listener = $this->listener;
        $listener($event);

        $parameterData = $event->getParam('LaminasContentNegotiationParameterData');
        $params        = $parameterData->getBodyParams();
        $this->assertEquals([
            'mime_type' => 'md',
        ], $params);

        $files = $request->getFiles();
        $this->assertEquals(1, $files->count());
        $file = $files->get('text');
        $this->assertIsArray($file);
        $this->assertArrayHasKey('error', $file);
        $this->assertArrayHasKey('name', $file);
        $this->assertArrayHasKey('tmp_name', $file);
        $this->assertArrayHasKey('size', $file);
        $this->assertArrayHasKey('type', $file);
        $this->assertEquals('README.md', $file['name']);
        $this->assertMatchesRegularExpression('/^laminasc/', basename($file['tmp_name']));
        $this->assertTrue(file_exists($file['tmp_name']));
    }

    public function testDecodingMultipartFormDataWithFileRegistersFileCleanupEventListener(): void
    {
        $request = new Request();
        $request->setMethod('PATCH');
        $request->getHeaders()->addHeaderLine(
            'Content-Type',
            'multipart/form-data; boundary=6603ddd555b044dc9a022f3ad9281c20'
        );
        $request->setContent(file_get_contents(__DIR__ . '/TestAsset/multipart-form-data.txt'));

        $target = new TestAsset\EventTarget();
        $events = $this->getMockBuilder(EventManagerInterface::class)->getMock();
        $events->expects($this->once())
            ->method('attach')
            ->with(
                $this->equalTo('finish'),
                $this->equalTo([$this->listener, 'onFinish']),
                $this->equalTo(1000)
            );
        $target->events = $events;

        $event = new MvcEvent();
        $event->setTarget($target);
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $listener = $this->listener;
        $listener($event);
    }

    public function testOnFinishWillRemoveAnyUploadFilesUploadedByTheListener(): void
    {
        $tmpDir  = MultipartContentParser::getUploadTempDir();
        $tmpFile = tempnam($tmpDir, 'laminasc');
        file_put_contents($tmpFile, 'File created by ' . self::class);

        $files   = new Parameters([
            'test' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.txt',
                'type'     => 'text/plain',
                'tmp_name' => $tmpFile,
                'size'     => filesize($tmpFile),
            ],
        ]);
        $request = new Request();
        $request->setFiles($files);

        $event = new MvcEvent();
        $event->setRequest($request);

        $r = new ReflectionObject($this->listener);
        $p = $r->getProperty('uploadTmpDir');
        $p->setAccessible(true);
        $p->setValue($this->listener, $tmpDir);

        if (strpos(PHP_OS, 'Darwin') !== false) {
            $tmpFile = '/private/var/' . $tmpFile;
        }

        $this->listener->onFinish($event);
        $this->assertFileDoesNotExist($tmpFile);
    }

    public function testOnFinishDoesNotRemoveUploadFilesTheListenerDidNotCreate(): void
    {
        $tmpDir  = MultipartContentParser::getUploadTempDir();
        $tmpFile = tempnam($tmpDir, 'php');
        file_put_contents($tmpFile, 'File created by ' . self::class);

        $files   = new Parameters([
            'test' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.txt',
                'type'     => 'text/plain',
                'tmp_name' => $tmpFile,
                'size'     => filesize($tmpFile),
            ],
        ]);
        $request = new Request();
        $request->setFiles($files);

        $event = new MvcEvent();
        $event->setRequest($request);

        $this->listener->onFinish($event);
        $this->assertTrue(file_exists($tmpFile));
        unlink($tmpFile);
    }

    public function testOnFinishDoesNotRemoveUploadFilesThatHaveBeenMoved(): void
    {
        $tmpDir = sys_get_temp_dir() . '/' . str_replace('\\', '_', self::class);
        mkdir($tmpDir);
        $tmpFile = tempnam($tmpDir, 'laminasc');

        $files   = new Parameters([
            'test' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.txt',
                'type'     => 'text/plain',
                'tmp_name' => $tmpFile,
            ],
        ]);
        $request = new Request();
        $request->setFiles($files);

        $event = new MvcEvent();
        $event->setRequest($request);

        $this->listener->onFinish($event);
        $this->assertTrue(file_exists($tmpFile));
        unlink($tmpFile);
        rmdir($tmpDir);
    }

    /**
     * @group 35
     * @dataProvider methodsWithBodies
     */
    public function testWillNotAttemptToInjectNullValueForBodyParams(string $method): void
    {
        $listener = $this->listener;

        $request = new Request();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $request->setContent('');

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $result = $listener($event);
        $this->assertNull($result);
        $params = $event->getParam('LaminasContentNegotiationParameterData');
        $this->assertEquals([], $params->getBodyParams());
    }

    /** @psalm-return array<string, array{0: string, 1: string}> */
    public function methodsWithBlankBodies(): array
    {
        return [
            'post-space'             => ['POST', ' '],
            'post-lines'             => ['POST', "\n\n"],
            'post-lines-and-space'   => ['POST', "  \n  \n"],
            'patch-space'            => ['PATCH', ' '],
            'patch-lines'            => ['PATCH', "\n\n"],
            'patch-lines-and-space'  => ['PATCH', "  \n  \n"],
            'put-space'              => ['PUT', ' '],
            'put-lines'              => ['PUT', "\n\n"],
            'put-lines-and-space'    => ['PUT', "  \n  \n"],
            'delete-space'           => ['DELETE', ' '],
            'delete-lines'           => ['DELETE', "\n\n"],
            'delete-lines-and-space' => ['DELETE', "  \n  \n"],
        ];
    }

    /**
     * @param mixed $content
     * @group 36
     * @dataProvider methodsWithBlankBodies
     */
    public function testWillNotAttemptToInjectNullValueForBodyParamsWhenContentIsWhitespace(
        string $method,
        string $content
    ): void {
        $listener = $this->listener;

        $request = new Request();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $request->setContent($content);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $result = $listener($event);
        $this->assertNull($result);
        $params = $event->getParam('LaminasContentNegotiationParameterData');
        $this->assertEquals([], $params->getBodyParams());
    }

    /** @psalm-return array<string, array{0: string, 1: string}> */
    public function methodsWithLeadingWhitespace(): array
    {
        return [
            'post-space'             => ['POST', ' {"foo": "bar"}'],
            'post-lines'             => ['POST', "\n\n{\"foo\": \"bar\"}"],
            'post-lines-and-space'   => ['POST', "  \n  \n{\"foo\": \"bar\"}"],
            'patch-space'            => ['PATCH', ' {"foo": "bar"}'],
            'patch-lines'            => ['PATCH', "\n\n{\"foo\": \"bar\"}"],
            'patch-lines-and-space'  => ['PATCH', "  \n  \n{\"foo\": \"bar\"}"],
            'put-space'              => ['PUT', ' {"foo": "bar"}'],
            'put-lines'              => ['PUT', "\n\n{\"foo\": \"bar\"}"],
            'put-lines-and-space'    => ['PUT', "  \n  \n{\"foo\": \"bar\"}"],
            'delete-space'           => ['DELETE', ' {"foo": "bar"}'],
            'delete-lines'           => ['DELETE', "\n\n{\"foo\": \"bar\"}"],
            'delete-lines-and-space' => ['DELETE', "  \n  \n{\"foo\": \"bar\"}"],
        ];
    }

    /**
     * @param mixed $content
     * @group 36
     * @dataProvider methodsWithLeadingWhitespace
     */
    public function testWillHandleJsonContentWithLeadingWhitespace(string $method, string $content): void
    {
        $listener = $this->listener;

        $request = new Request();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $request->setContent($content);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $result = $listener($event);
        $this->assertNull($result);
        $params = $event->getParam('LaminasContentNegotiationParameterData');
        $this->assertEquals(['foo' => 'bar'], $params->getBodyParams());
    }

    /** @psalm-return array<string, array{0: string, 1: string}> */
    public function methodsWithTrailingWhitespace(): array
    {
        return [
            'post-space'             => ['POST', '{"foo": "bar"} '],
            'post-lines'             => ['POST', "{\"foo\": \"bar\"}\n\n"],
            'post-lines-and-space'   => ['POST', "{\"foo\": \"bar\"}  \n  \n"],
            'patch-space'            => ['PATCH', '{"foo": "bar"} '],
            'patch-lines'            => ['PATCH', "{\"foo\": \"bar\"}\n\n"],
            'patch-lines-and-space'  => ['PATCH', "{\"foo\": \"bar\"}  \n  \n"],
            'put-space'              => ['PUT', '{"foo": "bar"} '],
            'put-lines'              => ['PUT', "{\"foo\": \"bar\"}\n\n"],
            'put-lines-and-space'    => ['PUT', "{\"foo\": \"bar\"}  \n  \n"],
            'delete-space'           => ['DELETE', '{"foo": "bar"} '],
            'delete-lines'           => ['DELETE', "{\"foo\": \"bar\"}\n\n"],
            'delete-lines-and-space' => ['DELETE', "{\"foo\": \"bar\"}  \n  \n"],
        ];
    }

    /**
     * @param mixed $content
     * @group 36
     * @dataProvider methodsWithTrailingWhitespace
     */
    public function testWillHandleJsonContentWithTrailingWhitespace(string $method, string $content): void
    {
        $listener = $this->listener;

        $request = new Request();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $request->setContent($content);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $result = $listener($event);
        $this->assertNull($result);
        $params = $event->getParam('LaminasContentNegotiationParameterData');
        $this->assertEquals(['foo' => 'bar'], $params->getBodyParams());
    }

    /** @psalm-return array<string, array{0: string, 1: string}> */
    public function methodsWithLeadingAndTrailingWhitespace(): array
    {
        return [
            'post-space'             => ['POST', ' {"foo": "bar"} '],
            'post-lines'             => ['POST', "\n\n{\"foo\": \"bar\"}\n\n"],
            'post-lines-and-space'   => ['POST', "  \n  \n{\"foo\": \"bar\"}  \n  \n"],
            'patch-space'            => ['PATCH', ' {"foo": "bar"} '],
            'patch-lines'            => ['PATCH', "\n\n{\"foo\": \"bar\"}\n\n"],
            'patch-lines-and-space'  => ['PATCH', "  \n  \n{\"foo\": \"bar\"}  \n  \n"],
            'put-space'              => ['PUT', ' {"foo": "bar"} '],
            'put-lines'              => ['PUT', "\n\n{\"foo\": \"bar\"}\n\n"],
            'put-lines-and-space'    => ['PUT', "  \n  \n{\"foo\": \"bar\"}  \n  \n"],
            'delete-space'           => ['DELETE', ' {"foo": "bar"} '],
            'delete-lines'           => ['DELETE', "\n\n{\"foo\": \"bar\"}\n\n"],
            'delete-lines-and-space' => ['DELETE', "  \n  \n{\"foo\": \"bar\"}  \n  \n"],
        ];
    }

    /**
     * @param mixed $content
     * @group 36
     * @dataProvider methodsWithLeadingAndTrailingWhitespace
     */
    public function testWillHandleJsonContentWithLeadingAndTrailingWhitespace(string $method, string $content): void
    {
        $listener = $this->listener;

        $request = new Request();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $request->setContent($content);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $result = $listener($event);
        $this->assertNull($result);
        $params = $event->getParam('LaminasContentNegotiationParameterData');
        $this->assertEquals(['foo' => 'bar'], $params->getBodyParams());
    }

    /** @psalm-return array<string, array{0: string, 1: string}> */
    public function methodsWithWhitespaceInsideBody(): array
    {
        return [
            'post-space'   => ['POST', '{"foo": "bar foo"}'],
            'patch-space'  => ['PATCH', '{"foo": "bar foo"}'],
            'put-space'    => ['PUT', '{"foo": "bar foo"}'],
            'delete-space' => ['DELETE', '{"foo": "bar foo"}'],
        ];
    }

    /**
     * @param mixed $content
     * @dataProvider methodsWithWhitespaceInsideBody
     */
    public function testWillNotRemoveWhitespaceInsideBody(string $method, string $content): void
    {
        $listener = $this->listener;

        $request = new Request();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $request->setContent($content);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $result = $listener($event);
        $this->assertNull($result);
        $params = $event->getParam('LaminasContentNegotiationParameterData');
        $this->assertEquals(['foo' => 'bar foo'], $params->getBodyParams());
    }

    /**
     * @group 42
     */
    public function testReturns400ResponseWhenBodyPartIsMissingName(): void
    {
        $request = new Request();
        $request->setMethod('PUT');
        $request->getHeaders()->addHeaderLine(
            'Content-Type',
            'multipart/form-data; boundary=6603ddd555b044dc9a022f3ad9281c20'
        );
        $request->setContent(file_get_contents(__DIR__ . '/TestAsset/multipart-form-data-missing-name.txt'));

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $listener = $this->listener;
        $result   = $listener($event);

        $this->assertInstanceOf(ApiProblemResponse::class, $result);
        $this->assertEquals(400, $result->getStatusCode());
        $details = $result->getApiProblem()->toArray();
        $this->assertStringContainsString('does not contain a "name" field', $details['detail']);
    }

    public function testReturnsArrayWhenFieldNamesHaveArraySyntax(): void
    {
        $request = new Request();
        $request->setMethod('PUT');
        $request->getHeaders()->addHeaderLine(
            'Content-Type',
            'multipart/form-data; boundary=6603ddd555b044dc9a022f3ad9281c20'
        );
        $request->setContent(file_get_contents(__DIR__ . '/TestAsset/multipart-form-data-array.txt'));
        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));
        $listener = $this->listener;
        $listener($event);
        $parameterData = $event->getParam('LaminasContentNegotiationParameterData');
        $params        = $parameterData->getBodyParams();
        $this->assertEquals([
            'string_value' => 'string_value_with&amp;ersand',
            'array_name'   => [
                'array_name[0]',
                'array_name[1]',
                'a' => 'array_name[a]',
                'b' => [
                    0   => 'array_name[b][0]',
                    'b' => 'array_name[b][b]',
                ],
            ],
        ], $params);
    }

    /**
     * @group 50
     * @dataProvider methodsWithBodies
     */
    public function testMergesHalEmbeddedPropertiesIntoTopLevelObjectWhenDecodingHalJson(string $method): void
    {
        $data = [
            'foo'       => 'bar',
            '_embedded' => [
                'bar' => [
                    'baz' => 'bat',
                ],
            ],
        ];
        $json = json_encode($data);

        $listener = $this->listener;

        $request = new Request();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/hal+json');
        $request->setContent($json);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $result = $listener($event);
        $this->assertNull($result);
        $params = $event->getParam('LaminasContentNegotiationParameterData');

        $expected = [
            'foo' => 'bar',
            'bar' => [
                'baz' => 'bat',
            ],
        ];

        $this->assertEquals($expected, $params->getBodyParams());
    }

    /** @psalm-return array<string, array{0:string, 1: string, 2: string|int}> */
    public function methodsWithStringContent(): array
    {
        return [
            'delete-string'      => ['DELETE', 'String Content', 'String_Content'],
            'delete-zero-key'    => ['DELETE', '0=', 0],
            'delete-empty-value' => ['DELETE', 'ids=', 'ids'],
            'patch-string'       => ['PATCH', '@String-Content', '@String-Content'],
            'patch-zero-key'     => ['PATCH', '0=', 0],
            'patch-empty-value'  => ['PATCH', 'name=', 'name'],
            'put-string'         => ['PUT', 'string.content', 'string_content'],
            'put-zero-key'       => ['PUT', '0=', 0],
            'put-empty-value'    => ['PUT', 'key=', 'key'],
        ];
    }

    /**
     * @dataProvider methodsWithStringContent
     * @param string|int $key
     */
    public function testStringContentIsParsedCorrectlyToAnArray(string $method, string $data, $key): void
    {
        $listener = $this->listener;

        $request = new Request();
        $request->setMethod($method);
        $request->setContent($data);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $result = $listener($event);
        $this->assertNull($result);

        /** @var ParameterDataContainer $params */
        $params = $event->getParam('LaminasContentNegotiationParameterData');
        $array  = $params->getBodyParams();

        $this->assertArrayHasKey($key, $array);
        $this->assertSame('', $array[$key]);
    }

    /** @psalm-return iterable<string, array{0: string, 1: string, 2: array|object}> */
    public function nonPostMethodsContent(): iterable
    {
        $dataSets = [
            'object' => [
                'data'     => '{"key": "value"}',
                'expected' => ['key' => 'value'],
            ],
            'array'  => [
                'data'     => '["first", "second"]',
                'expected' => ['first', 'second'],
            ],
            /** @see https://github.com/zfcampus/zf-content-negotiation/pull/96 */
            'empty' => [
                'data'     => '',
                'expected' => [],
            ],
        ];

        foreach (['PUT', 'PATCH', 'DELETE'] as $method) {
            foreach ($dataSets as $type => $set) {
                $name = sprintf('%s-%s', $type, $method);
                yield $name => [$method, $set['data'], $set['expected']];
            }
        }
    }

    /**
     * @see https://github.com/zfcampus/zf-content-negotiation/pull/94
     * @see https://github.com/zfcampus/zf-content-negotiation/pull/96
     *
     * @dataProvider nonPostMethodsContent
     * @param array|object $expected Expected body params
     */
    public function testMissingContentTypeHeaderResultsInParsingAsJsonIfInitialCharacterIndicatesObjectOrArray(
        string $method,
        string $data,
        $expected
    ): void {
        $listener = $this->listener;

        $request = new Request();
        $request->setMethod($method);
        $request->setContent($data);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($this->createRouteMatch([]));

        $result = $listener($event);
        $this->assertNull($result);

        /** @var ParameterDataContainer $params */
        $params = $event->getParam('LaminasContentNegotiationParameterData');
        $test   = $params->getBodyParams();

        $this->assertSame($expected, $test);
    }
}
