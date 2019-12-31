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
use Laminas\ApiTools\ContentNegotiation\HttpMethodOverrideListener;
use Laminas\ApiTools\ContentNegotiation\JsonModel;
use Laminas\Filter\File\RenameUpload;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Validator\File\UploadFile;

return [
    'filters' => [
        'factories' => [
            // Overwrite RenameUpload filter's factory
            RenameUpload::class => Factory\RenameUploadFilterFactory::class,

            // v2 support
            'laminasfilterfilerenameupload' => Factory\RenameUploadFilterFactory::class,
        ],
    ],

    'validators' => [
        'factories'   => [
            // Overwrite UploadFile validator's factory
            UploadFile::class => Factory\UploadFileValidatorFactory::class,

            // v2 support
            'laminasvalidatorfileuploadfile' => Factory\UploadFileValidatorFactory::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            ContentTypeListener::class       => InvokableFactory::class,
            'Request'                        => Factory\RequestFactory::class,
            AcceptListener::class            => Factory\AcceptListenerFactory::class,
            AcceptFilterListener::class      => Factory\AcceptFilterListenerFactory::class,
            ContentTypeFilterListener::class => Factory\ContentTypeFilterListenerFactory::class,
            ContentNegotiationOptions::class => Factory\ContentNegotiationOptionsFactory::class,
            HttpMethodOverrideListener::class => Factory\HttpMethodOverrideListenerFactory::class,
        ],
    ],

    'api-tools-content-negotiation' => [
        // This is an array of controller service names pointing to one of:
        // - a named selector (see below)
        // - an array of specific selectors, in the same format as for the
        //   selectors key
        'controllers'            => [],

        // This is an array of named selectors. Each selector consists of a
        // view model type pointing to the Accept mediatypes that will trigger
        // selection of that view model; see the documentation on the
        // AcceptableViewModelSelector plugin for details on the format:
        // http://docs.laminas.dev/laminas-mvc/plugins/#acceptableviewmodelselector-plugin
        'selectors'              => [
            'Json' => [
                JsonModel::class => [
                    'application/json',
                    'application/*+json',
                ],
            ],
        ],

        // Array of controller service name => allowed accept header pairs.
        // The allowed content type may be a string, or an array of strings.
        'accept_whitelist'       => [],

        // Array of controller service name => allowed content type pairs.
        // The allowed content type may be a string, or an array of strings.
        'content_type_whitelist' => [],

        // Enable x-http method override feature
        // When set to 'true' the  http method in the request will be overridden
        // by the method inside the 'X-HTTP-Method-Override' header (if present)
        'x_http_method_override_enabled' => false,

        // Map incoming HTTP request methods to acceptable X-HTTP-Method-Override
        // values; when matched, the override value will be used for the incoming
        // request.
        'http_override_methods' => [
            // Example:
            // The following allows the X-HTTP-Method-Override header to override
            // a GET request using one of the values in the supplied array:
            // 'GET' => ['HEAD', 'POST', 'PUT', 'DELETE', 'PATCH']
        ],
    ],

    'controller_plugins' => [
        'aliases' => [
            'routeParam'  => ControllerPlugin\RouteParam::class,
            'queryParam'  => ControllerPlugin\QueryParam::class,
            'bodyParam'   => ControllerPlugin\BodyParam::class,
            'routeParams' => ControllerPlugin\RouteParams::class,
            'queryParams' => ControllerPlugin\QueryParams::class,
            'bodyParams'  => ControllerPlugin\BodyParams::class,
        ],
        'factories' => [
            ControllerPlugin\RouteParam::class  => InvokableFactory::class,
            ControllerPlugin\QueryParam::class  => InvokableFactory::class,
            ControllerPlugin\BodyParam::class   => InvokableFactory::class,
            ControllerPlugin\RouteParams::class => InvokableFactory::class,
            ControllerPlugin\QueryParams::class => InvokableFactory::class,
            ControllerPlugin\BodyParams::class  => InvokableFactory::class,
        ],
    ],
];
