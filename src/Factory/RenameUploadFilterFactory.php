<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Laminas\ApiTools\ContentNegotiation\Filter\RenameUpload;

class RenameUploadFilterFactory
{
    /**
     * @var null|array|\Traversable
     */
    protected $creationOptions;

    /**
     * @param null|array|\Traversable $options
     */
    public function __construct($options = null)
    {
        $this->creationOptions = $options;
    }

    /**
     * Create a RenameUpload instance
     *
     * @param \Laminas\Filter\FilterPluginManager $filters
     * @return RenameUpload
     */
    public function __invoke($filters)
    {
        $services = $filters->getServiceLocator();
        $filter   = new RenameUpload($this->creationOptions);
        if ($services->has('Request')) {
            $filter->setRequest($services->get('Request'));
        }
        return $filter;
    }
}
