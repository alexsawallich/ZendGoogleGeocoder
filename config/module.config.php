<?php
return [
    'service_manager' => [
        'factories' => [
            'ZendGoogleGeocoderService' => function ($sm) {
                return new \ZendGoogleGeocoder\Service\GeocoderService($sm);
            }
        ],
        'invokables' => [
            'GoogleGeocoderApi' => '\ZendGoogleGeocoder\Service\GeocoderApi'
        ]
    ]
];
