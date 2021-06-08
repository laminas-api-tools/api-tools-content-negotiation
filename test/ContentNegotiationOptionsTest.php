<?php

namespace LaminasTest\ApiTools\ContentNegotiation;

use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;
use PHPUnit\Framework\TestCase;

class ContentNegotiationOptionsTest extends TestCase
{
    /** @psalm-return array<string, array{0: string, 1: string}> */
    public function dashSeparatedOptions(): array
    {
        return [
            'accept-whitelist'               => ['accept-whitelist', 'accept_whitelist'],
            'content-type-whitelist'         => ['content-type-whitelist', 'content_type_whitelist'],
            'x-http-method-override-enabled' => ['x-http-method-override-enabled', 'x_http_method_override_enabled'],
            'http-override-methods'          => ['http-override-methods', 'http_override_methods'],
        ];
    }

    /**
     * @dataProvider dashSeparatedOptions
     */
    public function testSetNormalizesDashSeparatedKeysToUnderscoreSeparated(string $key, string $normalized): void
    {
        $options         = new ContentNegotiationOptions();
        $options->{$key} = ['value'];
        $this->assertEquals(['value'], $options->{$key});
        $this->assertEquals(['value'], $options->{$normalized});
    }

    /**
     * @dataProvider dashSeparatedOptions
     */
    public function testConstructorAllowsDashSeparatedKeys(string $key, string $normalized): void
    {
        $options = new ContentNegotiationOptions([$key => ['value']]);
        $this->assertEquals(['value'], $options->{$key});
        $this->assertEquals(['value'], $options->{$normalized});
    }

    /**
     * @dataProvider dashSeparatedOptions
     */
    public function testDashAndUnderscoreSeparatedValuesGetMerged(string $key, string $normalized): void
    {
        $keyValue        = 'valueKey';
        $normalizedValue = 'valueNormalized';
        $expectedResult  = [
            $keyValue,
            $normalizedValue,
        ];

        $options = new ContentNegotiationOptions();
        $options->setFromArray(
            [
                $key        => [
                    $keyValue,
                ],
                $normalized => [
                    $normalizedValue,
                ],
            ]
        );

        $this->assertEquals(
            $expectedResult,
            $options->{$key},
            'The value for the hyphen separated key was not as expected.'
        );
        $this->assertEquals(
            $expectedResult,
            $options->{$normalized},
            'The value for the normalized key was not as expected.'
        );
    }
}
