<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

use Laminas\ApiTools\ContentNegotiation\AcceptFilterListener;
use Laminas\ApiTools\ContentNegotiation\AcceptListener;
use Laminas\ApiTools\ContentNegotiation\ContentNegotiationOptions;
use Laminas\ApiTools\ContentNegotiation\ContentTypeFilterListener;
use Laminas\ApiTools\ContentNegotiation\ContentTypeListener;
use Laminas\ApiTools\ContentNegotiation\ControllerPlugin;
use Laminas\ApiTools\ContentNegotiation\Factory;
use Laminas\ApiTools\ContentNegotiation\JsonModel;

return [
    'filters' => [
        'aliases'   => [
            'Laminas\Filter\File\RenameUpload' => 'filerenameupload',
        ],
        'factories' => [
            'filerenameupload' => Factory\RenameUploadFilterFactory::class,
        ],
    ],

    'validators' => [
        'aliases'   => [
            'Laminas\Validator\File\UploadFile' => 'fileuploadfile',
        ],
        'factories' => [
            'fileuploadfile' => Factory\UploadFileValidatorFactory::class,
        ],
    ],

    'service_manager' => [
        'invokables' => [
            ContentTypeListener::class => ContentTypeListener::class,
        ],
        'factories' => [
            'Request'                        => Factory\RequestFactory::class,
            AcceptListener::class            => Factory\AcceptListenerFactory::class,
            AcceptFilterListener::class      => Factory\AcceptFilterListenerFactory::class,
            ContentTypeFilterListener::class => Factory\ContentTypeFilterListenerFactory::class,
            ContentNegotiationOptions::class => Factory\ContentNegotiationOptionsFactory::class,
        ],
    ],

    'api-tools-content-negotiation' => [
        // This is an array of controller service names pointing to one of:
        // - a named selector (see below)
        // - an array of specific selectors, in the same format as for the
        //   selectors key
        'controllers' => [],

        // This is an array of named selectors. Each selector consists of a
        // view model type pointing to the Accept mediatypes that will trigger
        // selection of that view model; see the documentation on the
        // AcceptableViewModelSelector plugin for details on the format:
        // http://docs.laminas.dev/laminas-mvc/plugins/#acceptableviewmodelselector-plugin
        'selectors'   => [
            'Json' => [
                JsonModel::class => [
                    'application/json',
                    'application/*+json',
                ],
            ],
        ],

        // Array of controller service name => allowed accept header pairs.
        // The allowed content type may be a string, or an array of strings.
        'accept_whitelist' => [],

        // Array of controller service name => allowed content type pairs.
        // The allowed content type may be a string, or an array of strings.
        'content_type_whitelist' => [],
    ],

    'controller_plugins' => [
        'invokables' => [
            'routeParam'  => ControllerPlugin\RouteParam::class,
            'queryParam'  => ControllerPlugin\QueryParam::class,
            'bodyParam'   => ControllerPlugin\BodyParam::class,
            'routeParams' => ControllerPlugin\RouteParams::class,
            'queryParams' => ControllerPlugin\QueryParams::class,
            'bodyParams'  => ControllerPlugin\BodyParams::class,
        ],
    ],
];
