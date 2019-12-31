<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ContentNegotiation\JsonModel;
use Laminas\ApiTools\Hal\Collection as HalCollection;
use Laminas\ApiTools\Hal\Entity as HalEntity;
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
}
