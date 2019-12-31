<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

return array(
    'filters' => array(
        'aliases'   => array(
            'Laminas\Filter\File\RenameUpload' => 'filerenameupload',
        ),
        'factories' => array(
            'filerenameupload' => 'Laminas\ApiTools\ContentNegotiation\Factory\RenameUploadFilterFactory',
        ),
    ),

    'validators' => array(
        'aliases'   => array(
            'Laminas\Validator\File\UploadFile' => 'fileuploadfile',
        ),
        'factories' => array(
            'fileuploadfile' => 'Laminas\ApiTools\ContentNegotiation\Factory\UploadFileValidatorFactory',
        ),
    ),

    'service_manager' => array(
        'invokables' => array(
            'Laminas\ApiTools\ContentNegotiation\ContentTypeListener' => 'Laminas\ApiTools\ContentNegotiation\ContentTypeListener',
        ),
        'factories' => array(
            'Request'                                         => 'Laminas\ApiTools\ContentNegotiation\Factory\RequestFactory',
            'Laminas\ApiTools\ContentNegotiation\AcceptListener'            => 'Laminas\ApiTools\ContentNegotiation\Factory\AcceptListenerFactory',
            'Laminas\ApiTools\ContentNegotiation\AcceptFilterListener'      => 'Laminas\ApiTools\ContentNegotiation\Factory\AcceptFilterListenerFactory',
            'Laminas\ApiTools\ContentNegotiation\ContentTypeFilterListener' => 'Laminas\ApiTools\ContentNegotiation\Factory\ContentTypeFilterListenerFactory',
        )
    ),

    'api-tools-content-negotiation' => array(
        // This is an array of controller service names pointing to one of:
        // - a named selector (see below)
        // - an array of specific selectors, in the same format as for the
        //   selectors key
        'controllers' => array(),

        // This is an array of named selectors. Each selector consists of a
        // view model type pointing to the Accept mediatypes that will trigger
        // selection of that view model; see the documentation on the
        // AcceptableViewModelSelector plugin for details on the format:
        // http://laminas.readthedocs.org/en/latest/modules/laminas.mvc.plugins.html?highlight=acceptableviewmodelselector#acceptableviewmodelselector-plugin
        'selectors'   => array(
            'Json' => array(
                'Laminas\ApiTools\ContentNegotiation\JsonModel' => array(
                    'application/json',
                    'application/*+json',
                ),
            ),
        ),

        // Array of controller service name => allowed accept header pairs.
        // The allowed content type may be a string, or an array of strings.
        'accept_whitelist' => array(),

        // Array of controller service name => allowed content type pairs.
        // The allowed content type may be a string, or an array of strings.
        'content_type_whitelist' => array(),
    ),

    'controller_plugins' => array(
        'invokables' => array(
            'routeParam'  => 'Laminas\ApiTools\ContentNegotiation\ControllerPlugin\RouteParam',
            'queryParam'  => 'Laminas\ApiTools\ContentNegotiation\ControllerPlugin\QueryParam',
            'bodyParam'   => 'Laminas\ApiTools\ContentNegotiation\ControllerPlugin\BodyParam',
            'routeParams' => 'Laminas\ApiTools\ContentNegotiation\ControllerPlugin\RouteParams',
            'queryParams' => 'Laminas\ApiTools\ContentNegotiation\ControllerPlugin\QueryParams',
            'bodyParams'  => 'Laminas\ApiTools\ContentNegotiation\ControllerPlugin\BodyParams',
        )
    )
);
