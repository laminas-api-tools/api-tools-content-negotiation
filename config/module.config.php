<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

return array(
    'api-tools-content-negotiation' => array(
        'controllers' => array(),
        'selectors' => array(
            'Json' => array(
                'Laminas\ApiTools\ContentNegotiation\JsonModel' => array(
                    'application/json',
                    'application/*+json',
                ),
            ),
        ),
        'accept-whitelist' => array(
            // Array of controller service name => allowed accept header pairs.
            // The allowed content type may be a string, or an array of strings.
        ),
        'content-type-whitelist' => array(
            // Array of controller service name => allowed content type pairs.
            // The allowed content type may be a string, or an array of strings.
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'routeParam'                 => 'Laminas\ApiTools\ContentNegotiation\ControllerPlugin\RouteParam',
            'queryParam'                 => 'Laminas\ApiTools\ContentNegotiation\ControllerPlugin\QueryParam',
            'bodyParam'                  => 'Laminas\ApiTools\ContentNegotiation\ControllerPlugin\BodyParam',
            'routeParams'                => 'Laminas\ApiTools\ContentNegotiation\ControllerPlugin\RouteParams',
            'queryParams'                => 'Laminas\ApiTools\ContentNegotiation\ControllerPlugin\QueryParams',
            'bodyParams'                 => 'Laminas\ApiTools\ContentNegotiation\ControllerPlugin\BodyParams',
        )
    )
);
