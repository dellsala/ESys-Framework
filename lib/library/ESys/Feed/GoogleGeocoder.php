<?php

require_once 'ESys/Feed/HttpRequest.php';

/**
 * @package ESys
 */
class ESys_Feed_GoogleGeocoder {

    private $apiKey;
    private $cacheDirectory = null;
    private $lastStatus = null;


    /**
     * @param string $apiKey
     */
    public function __construct ($apiKey)
    {
        $this->apiKey = $apiKey;
    }


    /**
     * @param string $directory
     * @return void
     */
    public function setCacheDirectory ($directory)
    {
        $this->cacheDirectory = $directory;
    }


    /**
     * @return string
     */
    public function getApiKey ()
    {
        return $this->apiKey;
    }


    /**
     * @param string $addressString
     * @return void
     */
    public function geocode ($addressString)
    {
        $url = 'http://maps.google.com/maps/geo?';
        $url .= 'q='.rawurlencode($addressString);
        $url .= '&output=xml';
        $urlId = md5($url);
        $url .= '&key='.rawurlencode($this->apiKey);
        $cacheFile = $this->cacheDirectory.'/'.$urlId.'.geocode';
        if (isset($this->cacheDirectory) &&
            file_exists($cacheFile))
        {
            $xml = implode('', file($cacheFile));
        } else {
            $httpRequest = new ESys_Feed_HttpRequest($url);
            if (! $httpRequest->send()) {
                $this->lastStatus = -2;
                return false;
            }
            $xml = $httpRequest->getResponse();
            if (isset($this->cacheDirectory)
                && ($fh = fopen($cacheFile, 'w')))
            {
                fputs($fh, $xml);
                fclose($fh);
            }
        }
        $geocode = $this->_createGeocode($xml);
        if (! $geocode && file_exists($cacheFile)) {
            unlink($cacheFile);
        }
        return $geocode;
    }


    /**
     * @return string
     */
    public function status ()
    {
        return $this->lastStatus;
    }


    private function _createGeocode ($xml)
    {
        $data = simplexml_load_string(utf8_decode($xml));
        if (! $data) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): unable to parse xml response',
                E_USER_WARNING);
            $this->lastStatus = -1;
            return false;
        }
        if (! isset($data->Response->Status)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): unexpected response format',
                E_USER_WARNING);
            $this->lastStatus = -1;
            return false;
        }
        $this->lastStatus = $data->Response->Status->code;
        if ($this->lastStatus != '200')
        {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): google error response',
                E_USER_WARNING);
            return false;
        }
        if (! isset($data->Response->Placemark[0]->Point->coordinates)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): unexpected response format',
                E_USER_WARNING);
            $this->lastStatus = -1;
            return false;
        }
        $coords = explode(',', $data->Response->Placemark[0]->Point->coordinates);
        $geocode = new ESys_Feed_GoogleGeocode($coords[1], $coords[0]);
        return $geocode;
    }


    /**
     * @return string
     */
    public function statusMessage ()
    {
        $code = array(
            200 => 'No errors occurred; the address was successfully parsed and its geocode '.  
                'has been returned.',
            500 => 'A geocoding request could not be successfully processed, yet the exact '.
                'reason for the failure is not known.',
            601 => 'The HTTP q parameter was either missing or had no value.',
            602 => 'No corresponding geographic location could be found for the specified address.',
            603 => 'The geocode for the given address cannot be returned due to legal or contractual reasons.',
            610 => 'The given key is either invalid or does not match the domain for which it was given.',
            -1 => 'Data parsing error.',
            -2 => 'Network error.',
        );
        if (! isset($code[$this->lastStatus])) {
            return null;
        }
        return $code[$this->lastStatus];
    }

}


/**
 * @package ESys
 */
class ESys_Feed_GoogleGeocode {

    private $lat;
    private $long;


    /**
     * @param int
     * @param int
     */
    public function __construct ($lat, $long)
    {
        $this->lat = $lat;
        $this->long = $long;
    }
    
    /**
     * @return int
     */
    public function latitude ()
    {
        return $this->lat;
    }

    /**
     * @return int
     */
    public function longitude ()
    {
        return $this->long;
    }

}


