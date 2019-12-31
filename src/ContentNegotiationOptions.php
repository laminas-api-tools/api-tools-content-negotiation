<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation;

use Laminas\Stdlib\AbstractOptions;

class ContentNegotiationOptions extends AbstractOptions
{
    /**
     * @var array
     */
    protected $controllers = array();

    /**
     * @var array
     */
    protected $selectors = array();

    /**
     * @var array
     */
    protected $acceptWhitelist = array();

    /**
     * @var array
     */
    protected $contentTypeWhitelist = array();

    /**
     * @param array $controllers
     */
    public function setControllers(array $controllers)
    {
        $this->controllers = $controllers;
    }

    /**
     * @return array
     */
    public function getControllers()
    {
        return $this->controllers;
    }

    /**
     * @param array $selectors
     */
    public function setSelectors(array $selectors)
    {
        $this->selectors = $selectors;
    }

    /**
     * @return array
     */
    public function getSelectors()
    {
        return $this->selectors;
    }

    /**
     * @param array $whitelist
     */
    public function setAcceptWhitelist(array $whitelist)
    {
        $this->acceptWhitelist = $whitelist;
    }

    /**
     * @return array
     */
    public function getAcceptWhitelist()
    {
        return $this->acceptWhitelist;
    }

    /**
     * @param array $whitelist
     */
    public function setContentTypeWhitelist(array $whitelist)
    {
        $this->contentTypeWhitelist = $whitelist;
    }

    /**
     * @return array
     */
    public function getContentTypeWhitelist()
    {
        return $this->contentTypeWhitelist;
    }
}
