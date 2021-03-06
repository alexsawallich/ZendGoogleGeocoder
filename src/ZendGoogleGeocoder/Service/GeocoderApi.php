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
     * Logger-object.
     *
     * @var \Zend\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Options-object for this module.
     *
     * @var \ZendGoogleGeocoder\Options\GeocoderOptions
     */
    protected $options;

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
     * Stores the options into the API-Client.
     *
     * @param \ZendGoogleGeocoder\Options\GeocoderOptions $options
     */
    public function __construct(\ZendGoogleGeocoder\Options\GeocoderOptions $options, \Zend\Log\LoggerInterface $logger)
    {
        $this->options = $options;
        $this->logger = $logger;
    }

    /**
     * Checks the response for errors sent from the Google Geocoder API.
     *
     * @param string $response
     * @param string $format
     * @throws \Exception
     */
    protected function checkResponse($response, $format)
    {
        switch ($format) {
            case 'json':
                $json = json_decode($response);
                $status = $json->status;
                if ('OK' != $status && 'ZERO_RESULTS' != $status) {
                    $this->logger->err(sprintf('The Google Geocoder API responded with an error. The status was %s. Refer to the docs to see what that status means.', $status));
                    throw new \Exception(sprintf('The Google Geocoder API responded with an error. The status was %s. Refer to the docs to see what that status means.', $status));
                } else {
                    $this->logger->debug('Response OK');
                }
                break;
            
            case 'xml':
                $xml = new \SimpleXMLElement($response);
                $status = current($xml->status);
                if ('OK' != $status && 'ZERO_RESULTS' != $status) {
                    $this->logger->err(sprintf('The Google Geocoder API responded with an error. The status was %s. Refer to the docs to see what that status means.', $status));
                    throw new \Exception(sprintf('The Google Geocoder API responded with an error. The status was %s. Refer to the docs to see what that status means.', $status));
                } else {
                    $this->logger->debug('Response OK');
                }
                break;
        }
    }

    /**
     * Uses curl to request the api and returns the response.
     *
     * @param string $url
     * @param string $format
     * @return string
     */
    protected function doCurlRequest($url, $format)
    {
        $curlHandle = curl_init($url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curlHandle);
        
        if (false === $response) {
            $error = curl_error($curlHandle);
            curl_close($curlHandle);
            $this->logger->emerg(sprintf('CURL-Request failed with the following error: %s', $error));
            throw new \Exception(sprintf('CURL-Request failed with the following error: %s', $error), 300);
        }
        
        curl_close($curlHandle);
        
        $this->logger->debug('Checking response for errors.');
        $this->checkResponse($response, $format);
        
        return $response;
    }

    /**
     * Requests the given URL by using file_get_contents and returns the response.
     *
     * @param string $url
     * @param string $format
     * @return string
     */
    protected function doStreamRequest($url, $format)
    {
        $response = file_get_contents($url);
        
        $this->logger->debug('Checking response for errors.');
        $this->checkResponse($response, $format);
        
        return $response;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \ZendGoogleGeocoder\Service\GeocoderApiInterface::fetchGeoDataForAddress()
     */
    public function fetchGeoDataForAddress($address, $format = null)
    {
        if (null !== $format) {
            $this->validateFormat($format);
        } else {
            $format = $this->getDefaultFormat();
        }
        
        $this->logger->debug(sprintf('Generating URL for requesting the Google Geocoder API with format: %s', $format));
        $url = $this->generateRequestUrl($address, $format);
        
        if (true === $this->hasStreamSupport()) { // Use file_get_contents. It's faster, but not supported by every shared hoster.
            $this->logger->debug(sprintf('allow_url_fopen is enabled. Using file_get_contents to retrieve response.'));
            return $this->doStreamRequest($url, $format);
        } elseif (true === $this->hasCurlSupport()) { // Use curl. A bit overkill, but works fine.
            $this->logger->debug(sprintf('Using cURL to retrieve response.'));
            return $this->doCurlRequest($url, $format);
        } else {
            $this->logger->emerg('Unable to fire a HTTP-Request to the Google Geocoder API, since wether "allow_url_fopen" is enabled nor the mod_curl is installed.');
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
        
        $queryParams = [
            'address' => $address
        ];
        
        $key = $this->getKey();
        if (null != $key) {
            $queryParams['key'] = $key;
        }
        
        $queryString = http_build_query($queryParams);
        
        $url .= '?' . $queryString;
        
        if (true === isset($queryParams['key'])) {
            $queryParams['key'] = mb_substr($key, 0, 2) . str_repeat('x', strlen($key) - 4) . mb_substr($key, - 2);
            $queryString = http_build_query($queryParams);
        }
        $this->logger->debug(sprintf('Generated url: %s.', self::GEOCODER_API_URI . '?' . $queryString));
        
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
     * Returns the API-Key from the options, if set.
     *
     * @return string|null
     */
    protected function getKey()
    {
        return $this->options->getKey();
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
