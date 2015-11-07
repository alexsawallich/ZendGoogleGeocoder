<?php
return [
    'service_manager' => [
        'factories' => [
            'ZendGoogleGeocoderService' => function ($sm) {
                return new \ZendGoogleGeocoder\Service\GeocoderService($sm);
            },
            'ZendGoogleGeocoderOptions' => function ($sm) {
                $config = $sm->get('config');
                $config = (isset($config['zend-google-geocoder'])) ? $config['zend-google-geocoder'] : [];
                return new \ZendGoogleGeocoder\Options\GeocoderOptions($config);
            },
            'GoogleGeocoderApi' => function ($sm) {
                $options = $sm->get('ZendGoogleGeocoderOptions');
                return new \ZendGoogleGeocoder\Service\GeocoderApi($options);
            }
        ]
    ]
];
