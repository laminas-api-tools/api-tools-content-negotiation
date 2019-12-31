<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;
use PHPUnit_Framework_TestCase as TestCase;

class ContentNegotiationOptionsTest extends TestCase
{
    public function dashSeparatedOptions()
    {
        return array(
            'accept-whitelist' => array('accept-whitelist', 'accept_whitelist'),
            'content-type-whitelist' => array('content-type-whitelist', 'content_type_whitelist'),
        );
    }

    /**
     * @dataProvider dashSeparatedOptions
     */
    public function testSetNormalizesDashSeparatedKeysToUnderscoreSeparated($key, $normalized)
    {
        $options = new ContentNegotiationOptions();
        $options->{$key} = array('value');
        $this->assertEquals(array('value'), $options->{$key});
        $this->assertEquals(array('value'), $options->{$normalized});
    }

    /**
     * @dataProvider dashSeparatedOptions
     */
    public function testConstructorAllowsDashSeparatedKeys($key, $normalized)
    {
        $options = new ContentNegotiationOptions(array($key => array('value')));
        $this->assertEquals(array('value'), $options->{$key});
        $this->assertEquals(array('value'), $options->{$normalized});
    }
}
