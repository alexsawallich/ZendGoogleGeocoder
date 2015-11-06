<?php
namespace ZendGoogleGeocoder\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GeocoderService implements GeocoderApiAwareInterface, ServiceLocatorAwareInterface
{

    /**
     *
     * @var \ZendGoogleGeocoder\Service\GeocoderApi
     */
    protected $geocoderApi;

    /**
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Stores the ServiceLocator-Instance into the service.
     *
     * Even the setting of the ServiceLocator will be done through a initiator, because of the
     * implementet ServiceLocatorAwareInterface, this seems to be cleaner, since the Service wouldn't
     * make any sense without the ServiceLocator-Instance.
     *
     * @param ServiceLocatorInterface $serviceLocator            
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
    }

    /**
     * Geocodes the given address in the requested format (json or xml).
     *
     * If the address was geocoded before it will be served from cache otherwise the Google API will be used.
     *
     * @todo Implement caching
     * @param string $address            
     * @param string $format            
     * @return string
     */
    public function geocodeAddress($address, $format = null)
    {
        if (false) {
            // @todo Retrieve from cache
        } else {
            return $this->getGeocoderApi()->fetchGeoDataForAddress($address, $format);
        }
    }

    /**
     *
     * @return \ZendGoogleGeocoder\Service\GeocoderApi
     */
    public function getGeocoderApi()
    {
        // This is done to support lazy-loading. If caching is enabled and the address was found, we
        // don't need the API-Object at all.
        // This is also the reason why there is no initiator for the GeocoderApiAwareInterface.
        if (null == $this->geocoderApi) {
            $this->setGeocoderApi($this->getServiceLocator()
                ->get('GoogleGeocoderApi'));
        }
        
        return $this->geocoderApi;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::getServiceLocator()
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     *
     * @param \ZendGoogleGeocoder\Service\GeocoderApi $geocoderApi            
     */
    public function setGeocoderApi(\ZendGoogleGeocoder\Service\GeocoderApiInterface $geocoderApi)
    {
        $this->geocoderApi = $geocoderApi;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::setServiceLocator()
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}
