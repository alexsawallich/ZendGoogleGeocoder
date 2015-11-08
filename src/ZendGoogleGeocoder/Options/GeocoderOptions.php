<?php
namespace ZendGoogleGeocoder\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Options-Object for configuring the module.
 */
class GeocoderOptions extends AbstractOptions
{

    /**
     * The API-Key used for using the Google-API
     *
     * @var string
     */
    protected $key;

    /**
     * Returns the set Google-API-Key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the API-Key for using the Google-API.
     *
     * @param string $key            
     * @return \ZendGoogleGeocoder\Options\GeocoderOptions
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }
}
