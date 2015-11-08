<?php
namespace ZendGoogleGeocoder\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service to geocode addresses with Google's Geocoding API.
 */
class GeocoderService implements GeocoderApiAwareInterface, ServiceLocatorAwareInterface
{

    /**
     *
     * @var \Zend\Log\LoggerInterface
     */
    protected $logger;

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
        $cacheKey = md5($address);
        $this->getLogger()->info(sprintf('Requested to geocode address "%s". Generated Cache-Key is %s', $address, $cacheKey));
        
        if (false) {
            // @todo Retrieve from cache
            $this->getLogger()->info('Response could be served from cache.');
        } else {
            $this->getLogger()->info('Response could not be found in cache. Using the Google API to retrieve response.');
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
     * Returns the logger-object from the service-locator.
     *
     * @return \Zend\Log\LoggerInterface
     */
    public function getLogger()
    {
        if (null === $this->logger) {
            $this->logger = $this->getServiceLocator()->get('ZendGoogleGeocoderLogger');
        }
        
        return $this->logger;
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
