<?php
namespace ZendGoogleGeocoder\Service;

/**
 * This interface is mainly used for type-hinting.
 */
interface GeocoderApiInterface
{

    /**
     * Sends a HTTP-Request to the Google Geocoder API to geocode the given address and returns the result.
     *
     * @param string $address
     *            The address as string {@see https://developers.google.com/maps/documentation/geocoding/intro#geocoding}
     * @param string $format
     *            Can be "json" or "xml" {@see https://developers.google.com/maps/documentation/geocoding/intro#GeocodingRequests}
     * @throws \Exception
     */
    public function fetchGeoDataForAddress($address, $format = null);
}
