<?php
namespace ZendGoogleGeocoder;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\ModuleManager\Feature\AutoloaderProviderInterface::getAutoloaderConfig()
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                ]
            ]
        ];
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\ModuleManager\Feature\ConfigProviderInterface::getConfig()
     */
    public function getConfig()
    {
        return include (__DIR__ . '/config/module.config.php');
    }
}
