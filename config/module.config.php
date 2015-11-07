<?php
return [
    'service_manager' => [
        'factories' => [
            'GoogleGeocoderApi' => function ($sm) {
                $options = $sm->get('ZendGoogleGeocoderOptions');
                return new \ZendGoogleGeocoder\Service\GeocoderApi($options, $sm->get('ZendGoogleGeocoderLogger'));
            },
            'ZendGoogleGeocoderLogger' => function ($sm) {
                $logger = new \Zend\Log\Logger();
                $writer = new \Zend\Log\Writer\Noop();
                $logger->addWriter($writer);
                return $logger;
            },
            'ZendGoogleGeocoderOptions' => function ($sm) {
                $config = $sm->get('config');
                $config = (isset($config['zend-google-geocoder'])) ? $config['zend-google-geocoder'] : [];
                return new \ZendGoogleGeocoder\Options\GeocoderOptions($config);
            },
            'ZendGoogleGeocoderService' => function ($sm) {
                return new \ZendGoogleGeocoder\Service\GeocoderService($sm);
            }
        ]
    ]
];
