<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation\Factory;

use Laminas\ApiTools\ContentNegotiation\Validator\UploadFile;

class UploadFileValidatorFactory
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
     * Create an UploadFile instance
     *
     * @param \Laminas\Validator\ValidatorPluginManager $validators
     * @return UploadFile
     */
    public function __invoke($validators)
    {
        $services  = $validators->getServiceLocator();
        $validator = new UploadFile($this->creationOptions);
        if ($services->has('Request')) {
            $validator->setRequest($services->get('Request'));
        }
        return $validator;
    }
}
