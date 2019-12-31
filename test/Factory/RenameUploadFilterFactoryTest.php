<?php

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Laminas\Filter\FilterPluginManager;
use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;

class RenameUploadFilterFactoryTest extends TestCase
{
    protected $filters;

    protected function setUp()
    {
        $config = new Config(
            [
                'factories' => [
                    'filerenameupload' => 'Laminas\ApiTools\ContentNegotiation\Factory\RenameUploadFilterFactory',
                ],
            ]
        );
        $this->filters = new FilterPluginManager($config);
        $this->filters->setServiceLocator(new ServiceManager());
    }

    public function testMultipleFilters()
    {
        $optionsFilterOne = [
            'target' => 'SomeDir',
        ];

        $optionsFilterTwo = [
            'target' => 'OtherDir',
        ];

        $filter = $this->filters->get('filerenameupload', $optionsFilterOne);
        $this->assertEquals('SomeDir', $filter->getTarget());

        $otherFilter = $this->filters->get('filerenameupload', $optionsFilterTwo);
        $this->assertEquals('OtherDir', $otherFilter->getTarget());
    }
}
