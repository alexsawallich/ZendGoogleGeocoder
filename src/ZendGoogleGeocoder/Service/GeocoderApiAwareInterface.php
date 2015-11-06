<?php
namespace ZendGoogleGeocoder\Service;

interface GeocoderApiAwareInterface
{

    /**
     * Returns the GeocoderApi-Object.
     *
     * @return \ZendGoogleGeocoder\Service\GeocoderApiInterface
     */
    public function getGeocoderApi();

    /**
     * Stores the GeocoderApi-Object within this object.
     *
     * @param \ZendGoogleGeocoder\Service\GeocoderApiInterface $geocoderApi            
     * @return mixed
     */
    public function setGeocoderApi(\ZendGoogleGeocoder\Service\GeocoderApiInterface $geocoderApi);
}
