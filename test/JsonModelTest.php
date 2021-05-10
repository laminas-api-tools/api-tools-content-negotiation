<?php

namespace LaminasTest\ApiTools\ContentNegotiation;

use ArrayIterator;
use ArrayObject;
use Laminas\ApiTools\ContentNegotiation\JsonModel;
use Laminas\ApiTools\Hal\Collection as HalCollection;
use Laminas\ApiTools\Hal\Entity as HalEntity;
use PHPUnit\Framework\TestCase;

class JsonModelTest extends TestCase
{
    public function testSetVariables()
    {
        $jsonModel = new JsonModel(new TestAsset\ModelWithJson());
        $this->assertEquals('bar', $jsonModel->getVariable('foo'));
    }

    public function testJsonModelIsAlwaysTerminal()
    {
        $jsonModel = new JsonModel([]);
        $jsonModel->setTerminal(false);
        $this->assertTrue($jsonModel->terminate());
    }

    public function testWillPullHalEntityFromPayloadToSerialize()
    {
        $jsonModel = new JsonModel([
            'payload' => new HalEntity(['id' => 2, 'title' => 'Hello world'], 1),
        ]);
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
        $collection = [
            ['foo' => 'bar'],
            ['bar' => 'baz'],
            ['baz' => 'bat'],
        ];

        $jsonModel = new JsonModel([
            'payload' => new HalCollection($collection),
        ]);
        $json = $jsonModel->serialize();
        $data = json_decode($json, true);
        $this->assertEquals($collection, $data);
    }

    public function testWillRaiseExceptionIfErrorOccursEncodingJson()
    {
        // Provide data that cannot be serialized to JSON
        $data = ['foo' => pack('H*', 'c32e')];
        $jsonModel = new JsonModel($data);
        $this->expectException('Laminas\ApiTools\ContentNegotiation\Exception\InvalidJsonException');
        $jsonModel->serialize();
    }

    /**
     * @group 17
     */
    public function testCanSerializeTraversables()
    {
        $variables = [
            'some' => 'content',
            'nested' => new ArrayObject([
                'objects' => 'should also be serialized',
                'arbitrarily' => new ArrayIterator([
                    'as' => 'deep as you like',
                ]),
            ]),
        ];
        $iterator  = new ArrayIterator($variables);
        $jsonModel = new JsonModel($iterator);
        $json = $jsonModel->serialize();
        $data = json_decode($json, true);


        $expected = [
            'some' => 'content',
            'nested' => [
                'objects' => 'should also be serialized',
                'arbitrarily' => [
                    'as' => 'deep as you like',
                ],
            ],
        ];

        $this->assertEquals($expected, $data);
    }
}
