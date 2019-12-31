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
        return [
            'accept-whitelist' => ['accept-whitelist', 'accept_whitelist'],
            'content-type-whitelist' => ['content-type-whitelist', 'content_type_whitelist'],
            'x-http-method-override-enabled' => ['x-http-method-override-enabled', 'x_http_method_override_enabled'],
            'http-override-methods' => ['http-override-methods', 'http_override_methods'],
        ];
    }

    /**
     * @dataProvider dashSeparatedOptions
     */
    public function testSetNormalizesDashSeparatedKeysToUnderscoreSeparated($key, $normalized)
    {
        $options = new ContentNegotiationOptions();
        $options->{$key} = ['value'];
        $this->assertEquals(['value'], $options->{$key});
        $this->assertEquals(['value'], $options->{$normalized});
    }

    /**
     * @dataProvider dashSeparatedOptions
     */
    public function testConstructorAllowsDashSeparatedKeys($key, $normalized)
    {
        $options = new ContentNegotiationOptions([$key => ['value']]);
        $this->assertEquals(['value'], $options->{$key});
        $this->assertEquals(['value'], $options->{$normalized});
    }
}
