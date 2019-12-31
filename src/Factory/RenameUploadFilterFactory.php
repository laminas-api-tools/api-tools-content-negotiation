<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Laminas\ApiTools\ContentNegotiation\Filter\RenameUpload;
use Laminas\Filter\FilterPluginManager;
use Laminas\ServiceManager\MutableCreationOptionsInterface;
use Traversable;

class RenameUploadFilterFactory implements MutableCreationOptionsInterface
{
    /**
     * @var null|array|Traversable
     */
    protected $creationOptions;

    /**
     * @param null|array|Traversable $options
     */
    public function __construct($options = null)
    {
        $this->creationOptions = $options;
    }

    /**
     * @param array $options
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }

    /**
     * Create a RenameUpload instance
     *
     * @param  FilterPluginManager $filters
     * @return RenameUpload
     */
    public function __invoke(FilterPluginManager $filters)
    {
        $services = $filters->getServiceLocator();
        $filter   = new RenameUpload($this->creationOptions);
        if ($services->has('Request')) {
            $filter->setRequest($services->get('Request'));
        }

        return $filter;
    }
}
