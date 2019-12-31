<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation;

use ArrayIterator;
use ArrayObject;
use Laminas\ApiTools\ContentNegotiation\JsonModel;
use Laminas\ApiTools\Hal\Collection as HalCollection;
use Laminas\ApiTools\Hal\Entity as HalEntity;
use Laminas\Stdlib\ArrayUtils;
use PHPUnit_Framework_TestCase as TestCase;

class JsonModelTest extends TestCase
{
    public function testSetVariables()
    {
        $jsonModel = new JsonModel(new TestAsset\ModelWithJson());
        $this->assertEquals('bar', $jsonModel->getVariable('foo'));
    }

    public function testJsonModelIsAlwaysTerminal()
    {
        $jsonModel = new JsonModel(array());
        $jsonModel->setTerminal(false);
        $this->assertTrue($jsonModel->terminate());
    }

    public function testWillPullHalEntityFromPayloadToSerialize()
    {
        $jsonModel = new JsonModel(array(
            'payload' => new HalEntity(array('id' => 2, 'title' => 'Hello world'), 1),
        ));
        $json = $jsonModel->serialize();
        $data = json_decode($json, true);
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals(2, $data['id']);
        $this->assertArrayHasKey('title', $data);
        $this->assertEquals('Hello world', $data['title']);
    }

    public function testWillPullHalCollectionFromPayloadToSerialize()
    {
        $collection = array(
            array('foo' => 'bar'),
            array('bar' => 'baz'),
            array('baz' => 'bat'),
        );

        $jsonModel = new JsonModel(array(
            'payload' => new HalCollection($collection),
        ));
        $json = $jsonModel->serialize();
        $data = json_decode($json, true);
        $this->assertEquals($collection, $data);
    }

    public function testWillRaiseExceptionIfErrorOccursEncodingJson()
    {
        if (version_compare(PHP_VERSION, '5.5.0', 'lt')) {
            $this->markTestSkipped('This test only runs on 5.5 and up');
        }

        // Provide data that cannot be serialized to JSON
        $data = array('foo' => pack('H*', 'c32e'));
        $jsonModel = new JsonModel($data);
        $this->setExpectedException('Laminas\ApiTools\ContentNegotiation\Exception\InvalidJsonException');
        $jsonModel->serialize();
    }

    /**
     * @group 17
     */
    public function testCanSerializeTraversables()
    {
        $variables = array(
            'some' => 'content',
            'nested' => new ArrayObject(array(
                'objects' => 'should also be serialized',
                'arbitrarily' => new ArrayIterator(array(
                    'as' => 'deep as you like',
                )),
            )),
        );
        $iterator  = new ArrayIterator($variables);
        $jsonModel = new JsonModel($iterator);
        $json = $jsonModel->serialize();
        $data = json_decode($json, true);
        $this->assertEquals(ArrayUtils::iteratorToArray($iterator), $data);
    }
}
