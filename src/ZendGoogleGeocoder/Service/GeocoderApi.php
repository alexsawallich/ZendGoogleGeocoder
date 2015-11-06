<?php
namespace ZendGoogleGeocoder\Service;

/**
 * API-Client to communicate with the Google Geocoder HTTP API.
 */
class GeocoderApi implements GeocoderApiInterface
{

    /**
     * The API endpoint-uri where requests will be send to.
     *
     * @var string
     */
    const GEOCODER_API_URI = 'https://maps.googleapis.com/maps/api/geocode/';

    /**
     * The default-format in which responses from the API will be returned.
     *
     * @var string Can be "json" or "xml" {@see https://developers.google.com/maps/documentation/geocoding/intro#GeocodingRequests}
     */
    protected $defaultFormat = 'json';

    /**
     * Valid return-formats.
     *
     * @var array
     */
    protected $validFormats = [
        'json',
        'xml'
    ];

    /**
     * Uses curl to request the api and returns the response.
     *
     * @param string $url            
     * @return string
     */
    protected function doCurlRequest($url)
    {
        $curlHandle = curl_init($url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curlHandle);
        
        if (false === $response) {
            $error = curl_error($curlHandle);
            curl_close($curlHandle);
            throw new \Exception('CURL-Request failed with the following error: ' . $error, 300);
        }
        
        curl_close($curlHandle);
        return $response;
    }

    /**
     * Requests the given URL by using file_get_contents and returns the response.
     *
     * @param string $url            
     * @return string
     */
    protected function doStreamRequest($url)
    {
        return file_get_contents($url);
    }

    /**
     * Sends a HTTP-Request to the Google Geocoder API to geocode the given address and returns the result.
     *
     * @param string $address
     *            The address as string {@see https://developers.google.com/maps/documentation/geocoding/intro#geocoding}
     * @param string $format
     *            Can be "json" or "xml" {@see https://developers.google.com/maps/documentation/geocoding/intro#GeocodingRequests}
     * @throws \Exception
     */
    public function fetchGeoDataForAddress($address, $format = null)
    {
        if (null !== $format) {
            $this->validateFormat($format);
        } else {
            $format = $this->getDefaultFormat();
        }
        
        $url = $this->generateRequestUrl($address, $format);
        
        if (true === $this->hasStreamSupport()) { // Use file_get_contents. It's faster, but not supported by every shared hoster.
            return $this->doStreamRequest($url);
        } elseif (true === $this->hasCurlSupport()) { // Use curl. A bit overkill, but works fine.
            return $this->doCurlRequest($url);
        } else {
            throw new \Exception('Unable to fire a HTTP-Request to the Google Geocoder API, since wether "allow_url_fopen" is enabled nor the mod_curl is installed.', 200);
        }
    }

    /**
     * Generates the request url for querying the Google Geocoder API.
     *
     * @param string $address            
     * @param string $format            
     * @return string
     */
    protected function generateRequestUrl($address, $format)
    {
        $url = self::GEOCODER_API_URI;
        $url .= $format;
        
        $queryString = http_build_query([
            'address' => $address
        ]);
        
        $url .= '?' . $queryString;
        
        return $url;
    }

    /**
     * Returns the default format, responses from the API will be returned in.
     *
     * @return string
     */
    public function getDefaultFormat()
    {
        return $this->defaultFormat;
    }

    /**
     * Stores the default response format for API-Requests.
     *
     * @param string $format
     *            Can be "json" or "xml".
     * @return \ZendGoogleGeocoder\Service\GeocoderApi
     */
    public function setDefaultFormat($format)
    {
        $this->validateFormat($format);
        
        $this->defaultFormat = $format;
        return $this;
    }

    /**
     * Checks if the given format is within the valid-format-whitelist.
     *
     * @param string $format            
     * @throws \InvalidArgumentException
     * @return void
     */
    protected function validateFormat($format)
    {
        if (false === in_array($format, $this->validFormats)) {
            throw new \InvalidArgumentException('Invalid format. Must be one of the following strings: ' . implode(', ', $this->validFormats), 100);
        }
    }

    /**
     * Checks if "allow_url_fopen" is enabled.
     *
     * @return boolean
     */
    protected function hasStreamSupport()
    {
        return (bool) ini_get('allow_url_fopen');
    }

    /**
     * Checks if the CURL is available.
     *
     * @return boolean
     */
    protected function hasCurlSupport()
    {
        return (bool) function_exists('curl_version');
    }
}
