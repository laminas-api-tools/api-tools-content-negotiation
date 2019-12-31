<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ContentNegotiation\ContentTypeListener;
use Laminas\ApiTools\ContentNegotiation\MultipartContentParser;
use Laminas\ApiTools\ContentNegotiation\Request as ContentNegotiationRequest;
use Laminas\Http\Request;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Router\RouteMatch;
use Laminas\Stdlib\Parameters;
use PHPUnit_Framework_TestCase as TestCase;
use ReflectionObject;

class ContentTypeListenerTest extends TestCase
{
    public function setUp()
    {
        $this->listener = new ContentTypeListener();
    }

    public function methodsWithBodies()
    {
        return array(
            'post' => array('POST'),
            'patch' => array('PATCH'),
            'put' => array('PUT'),
        );
    }

    /**
     * @group 3
     * @dataProvider methodsWithBodies
     */
    public function testJsonDecodeErrorsReturnsProblemResponse($method)
    {
        $listener = $this->listener;

        $request = new Request();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $request->setContent('Invalid JSON data');

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch(new RouteMatch(array()));

        $result = $listener($event);
        $this->assertInstanceOf('Laminas\ApiTools\ApiProblem\ApiProblemResponse', $result);
        $problem = $result->getApiProblem();
        $this->assertEquals(400, $problem->status);
        $this->assertContains('JSON decoding', $problem->detail);
    }

    public function multipartFormDataMethods()
    {
        return array(
            'patch' => array('patch'),
            'put'   => array('put'),
        );
    }

    /**
     * @dataProvider multipartFormDataMethods
     */
    public function testCanDecodeMultipartFormDataRequestsForPutAndPatchOperations($method)
    {
        $request = new Request();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine('Content-Type', 'multipart/form-data; boundary=6603ddd555b044dc9a022f3ad9281c20');
        $request->setContent(file_get_contents( __DIR__ . '/TestAsset/multipart-form-data.txt'));

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch(new RouteMatch(array()));

        $listener = $this->listener;
        $result = $listener($event);

        $parameterData = $event->getParam('LaminasContentNegotiationParameterData');
        $params = $parameterData->getBodyParams();
        $this->assertEquals(array(
            'mime_type' => 'md',
        ), $params);

        $files = $request->getFiles();
        $this->assertEquals(1, $files->count());
        $file = $files->get('text');
        $this->assertInternalType('array', $file);
        $this->assertArrayHasKey('error', $file);
        $this->assertArrayHasKey('name', $file);
        $this->assertArrayHasKey('tmp_name', $file);
        $this->assertArrayHasKey('size', $file);
        $this->assertArrayHasKey('type', $file);
        $this->assertEquals('README.md', $file['name']);
        $this->assertRegexp('/^laminasc/', basename($file['tmp_name']));
        $this->assertTrue(file_exists($file['tmp_name']));
    }

    /**
     * @dataProvider multipartFormDataMethods
     */
    public function testCanDecodeMultipartFormDataRequestsFromStreamsForPutAndPatchOperations($method)
    {
        $request = new ContentNegotiationRequest();
        $request->setMethod($method);
        $request->getHeaders()->addHeaderLine('Content-Type', 'multipart/form-data; boundary=6603ddd555b044dc9a022f3ad9281c20');
        $request->setContentStream('file://' . realpath(__DIR__ . '/TestAsset/multipart-form-data.txt'));

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch(new RouteMatch(array()));

        $listener = $this->listener;
        $result = $listener($event);

        $parameterData = $event->getParam('LaminasContentNegotiationParameterData');
        $params = $parameterData->getBodyParams();
        $this->assertEquals(array(
            'mime_type' => 'md',
        ), $params);

        $files = $request->getFiles();
        $this->assertEquals(1, $files->count());
        $file = $files->get('text');
        $this->assertInternalType('array', $file);
        $this->assertArrayHasKey('error', $file);
        $this->assertArrayHasKey('name', $file);
        $this->assertArrayHasKey('tmp_name', $file);
        $this->assertArrayHasKey('size', $file);
        $this->assertArrayHasKey('type', $file);
        $this->assertEquals('README.md', $file['name']);
        $this->assertRegexp('/^laminasc/', basename($file['tmp_name']));
        $this->assertTrue(file_exists($file['tmp_name']));
    }

    public function testDecodingMultipartFormDataWithFileRegistersFileCleanupEventListener()
    {
        $request = new Request();
        $request->setMethod('PATCH');
        $request->getHeaders()->addHeaderLine('Content-Type', 'multipart/form-data; boundary=6603ddd555b044dc9a022f3ad9281c20');
        $request->setContent(file_get_contents( __DIR__ . '/TestAsset/multipart-form-data.txt'));

        $target = new TestAsset\EventTarget();
        $events = $this->getMock('Laminas\EventManager\EventManagerInterface');
        $events->expects($this->once())
            ->method('attach')
            ->with(
                $this->equalTo('finish'),
                $this->equalTo(array($this->listener, 'onFinish')),
                $this->equalTo(1000)
            );
        $target->events = $events;

        $event = new MvcEvent();
        $event->setTarget($target);
        $event->setRequest($request);
        $event->setRouteMatch(new RouteMatch(array()));

        $listener = $this->listener;
        $result = $listener($event);
    }

    public function testOnFinishWillRemoveAnyUploadFilesUploadedByTheListener()
    {
        $tmpDir  = MultipartContentParser::getUploadTempDir();
        $tmpFile = tempnam($tmpDir, 'laminasc');
        file_put_contents($tmpFile, 'File created by ' . __CLASS__);

        $files = new Parameters(array(
            'test' => array(
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.txt',
                'type'     => 'text/plain',
                'tmp_name' => $tmpFile,
                'size'     => filesize($tmpFile),
            ),
        ));
        $request = new Request();
        $request->setFiles($files);

        $event = new MvcEvent();
        $event->setRequest($request);

        $r = new ReflectionObject($this->listener);
        $p = $r->getProperty('uploadTmpDir');
        $p->setAccessible(true);
        $p->setValue($this->listener, $tmpDir);

        $this->listener->onFinish($event);
        $this->assertFalse(file_exists($tmpFile));
    }

    public function testOnFinishDoesNotRemoveUploadFilesTheListenerDidNotCreate()
    {
        $tmpDir  = MultipartContentParser::getUploadTempDir();
        $tmpFile = tempnam($tmpDir, 'php');
        file_put_contents($tmpFile, 'File created by ' . __CLASS__);

        $files = new Parameters(array(
            'test' => array(
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.txt',
                'type'     => 'text/plain',
                'tmp_name' => $tmpFile,
                'size'     => filesize($tmpFile),
            ),
        ));
        $request = new Request();
        $request->setFiles($files);

        $event = new MvcEvent();
        $event->setRequest($request);

        $this->listener->onFinish($event);
        $this->assertTrue(file_exists($tmpFile));
        unlink($tmpFile);
    }

    public function testOnFinishDoesNotRemoveUploadFilesThatHaveBeenMoved()
    {
        $tmpDir  = sys_get_temp_dir() . '/' . str_replace('\\', '_', __CLASS__);
        mkdir($tmpDir);
        $tmpFile = tempnam($tmpDir, 'laminasc');

        $files = new Parameters(array(
            'test' => array(
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.txt',
                'type'     => 'text/plain',
                'tmp_name' => $tmpFile,
            ),
        ));
        $request = new Request();
        $request->setFiles($files);

        $event = new MvcEvent();
        $event->setRequest($request);

        $this->listener->onFinish($event);
        $this->assertTrue(file_exists($tmpFile));
        unlink($tmpFile);
        rmdir($tmpDir);
    }
}
