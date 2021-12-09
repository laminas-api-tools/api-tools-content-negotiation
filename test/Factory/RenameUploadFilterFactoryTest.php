<?php

declare(strict_types=1);

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Laminas\ApiTools\ContentNegotiation\Factory\RenameUploadFilterFactory;
use Laminas\Filter\FilterPluginManager;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class RenameUploadFilterFactoryTest extends TestCase
{
    /** @var FilterPluginManager */
    protected $filters;

    protected function setUp(): void
    {
        $config        = [
            'factories' => [
                'filerenameupload' => RenameUploadFilterFactory::class,
            ],
        ];
        $this->filters = new FilterPluginManager(new ServiceManager(), $config);
    }

    public function testMultipleFilters(): void
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
