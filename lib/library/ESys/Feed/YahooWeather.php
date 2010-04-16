<?php

/**
 * Yahoo Weather API for php.
 *
 * @package ESys
 */
class ESys_Feed_YahooWeather
{


    private $data = null;
    private $xml = null;
    private $cacheDirectory = null;
    private $usingCachedData = false;


    /**
     * @param string $cacheDirectory
     */
    public function __construct ($cacheDirectory = null)
    {
        $this->cacheDirectory = rtrim($cacheDirectory, '/');
    }


    /**
     * @param string $zipcode
     * @param string $unit
     * @return array
     */
    public function getWeather ($zipcode, $unit = 'f') 
    {
        $result = false;
        if ($this->cacheDirectory) {
            $result = $this->_getCachedData($zipcode, $unit);
        } else {
            $result = $this->_getRemoteData($zipcode, $unit);
        }
        return $result;
    }


    private function _getRemoteData ($zipcode, $unit)
    {
        if ($unit != 'f' && $unit != 'c') {
            $unit = 'f';
        }
        $url = 'http://xml.weather.yahoo.com/forecastrss?p='.$zipcode.'&u='.$unit;
        if (ini_get('allow_url_fopen')) {
            $fileArray = @file($url);
            if (! $fileArray) { return false; }
            $xml = implode('', $fileArray);
        } else if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $xml = curl_exec($ch);
            if ($xml === false) { return false; }
        } else {
            trigger_error('ESys_YahooWeather::getWeather(): '.
                'remote file access not supported on this server', E_USER_WARNING);
            return false;
        }
        $this->xml = $xml;
        $this->data = simplexml_load_string($xml);
        return true;
    }


    private function _getCachedData ($zipcode, $unit)
    {
        $dataMd5 = md5($zipcode.$unit);
        $cacheFile = $this->cacheDirectory.'/'.$dataMd5.'.xml';
        $refreshCache = false;
        if (! file_exists($cacheFile)) {
            $refreshCache = true;
        } else {
            $now = time();
            $fileModifiedTime = filemtime($cacheFile);
            $cacheTime = 60 * 15;  // 15 minutes
            if ($now > ($fileModifiedTime + $cacheTime)) {
                $refreshCache = true;
            }
        }
        if ($refreshCache) {
            $result = $this->_getRemoteData($zipcode, $unit);
            if ($result) {
                $fh = fopen($cacheFile, 'w');
                fputs($fh, $this->xml);
                fclose($fh);
            }
            $this->usingCachedData = false;
            return $result;
        }
        $this->xml = file_get_contents($cacheFile);
        $this->data = simplexml_load_string($this->xml);
        $this->usingCachedData = true;
        return true;
    }


    /**
     * @return boolean
     */
    public function isDataFromCache ()
    {
        return $this->usingCachedData;
    }


    /**
     * @param boolean $noUnit
     * @return string
     */
    public function getTemperature ($noUnit = false)
    {
        if (! $this->data) { return false; }
        $result = $this->data->xpath('channel/item/yweather:condition');
        $unit = $noUnit ? '' : '°';
        return (string)$result[0]['temp'] . $unit;
    }


    /**
     * @return string
     */
    public function getText ()
    {
        if (! $this->data) { return false; }
        $result = $this->data->xpath('channel/item/yweather:condition');
        return (string)$result[0]['text'];
    }


    /**
     * @return string
     */
    public function getDate ()
    {
        if (! $this->data) { return false; }
        $result = $this->data->xpath('channel/item/yweather:condition');
        return (string)$result[0]['date'];
    }


    /**
     * @return string
     */
    public function getHtmlDescription ()
    {
        if (! $this->data) { return false; }
        $result = $this->data->xpath('channel/item/description');
        return (string)$result[0];
    }


    /**
     * @return string
     */
    public function getYahooLink ()
    {
        if (! $this->data) { return false; }
        $result = $this->data->xpath('channel/item/link');
        return (string)$result[0];
    }


    /**
     * @return string
     */
    public function getImageUrl ()
    {
        if (! $this->data) { return false; }
        $html = $this->getHtmlDescription();
        preg_match('/<img.*src="([^"]*)"/', $html, $matches);
        return $matches[1];
    }

}
