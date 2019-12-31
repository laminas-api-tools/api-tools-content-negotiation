<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation;

use Laminas\View\Model\JsonModel as BaseJsonModel;

class JsonModel extends BaseJsonModel
{
    /**
     * Mark view model as terminal by default (intended for use with APIs)
     *
     * @var bool
     */
    protected $terminate = true;
}
