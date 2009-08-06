<?php

require_once 'XML/Unserializer.php';
require_once 'Services/JSON.php';
require_once 'ESys/Feed/HttpRequest.php';
require_once 'ESys/Feed/BlipTvVideo.php';

define('ESYS_FEED_BLIPTV_URL', 'http://blip.tv/');


/**
 * The YouTube.com website.
 *
 * Provides infomation about users and videos
 * availble on YouTube.com.
 *
 * @package ESys
 */
class ESys_Feed_BlipTv {

    var $options;

    /**
     * @param array $options
     */
    public function __construct ($options = array())
    {
        $defaultOptions = array(
            'cacheDirectory' => null,
        );
        $this->options = array_merge($defaultOptions, $options);
    }


    /**
     * @param string $username
     * @return array Array|false of ESys_Feed_BlipTvVideo objects.
     */
    public function getVideosByPlaylist ($username)
    {
        $url = 'http://'.$username.'.blip.tv/'.
            '?skin=json'.
            '&s=posts';
        $data = $this->request($url, 'json');
        if (! $data) {
            trigger_error('ESys_Feed_BlipTv::getVideosByPlaylist(): '.
                'There was a problem retrieving data from BlipTv.', E_USER_NOTICE);
            return false;
        }
        $videoList = array();
        foreach ($data as $videoData) {
            $videoList[] = new ESys_Feed_BlipTvVideo($videoData['Post']);
        }
        return $videoList;
    }


    /**
     * @param string $videoId
     * @return ESys_Feed_BlipTvVideo|false
     */
    public function getVideoById ($videoId)
    {
        $url = ESYS_FEED_BLIPTV_URL.
            'file/'.urlencode($videoId).
            '?skin=json';
        $data = $this->request($url, 'json');
        if (! $data) {
            trigger_error('ESys_Feed_BlipTv::getVideoById(): '.
                'There was a problem retrieving data from BlipTv.', E_USER_NOTICE);
            return false;
        }
        $video = new ESys_Feed_BlipTvVideo($data[0]['Post']);
        return $video;
    }


    private function request ($requestUrl, $responseType = 'json')
    {
        if (isset($this->options['cacheDirectory'])) {
            $response = $this->_fetchCachedResponse($requestUrl, $responseType);
        } else {
            $response = $this->_fetchResponse($requestUrl, $responseType);
        }
        return $response;
    }


    private function _fetchResponse ($requestUrl, $responseType = 'xml')
    {
        $request = new ESys_Feed_HttpRequest($requestUrl);
        $request->send();
        $response = $request->getResponse();
        if (! $response) { return false; }
        if ($responseType == 'xml') {
            $unserializer = new XML_Unserializer(array(
                XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE => true,
                XML_UNSERIALIZER_OPTION_ATTRIBUTES_ARRAYKEY => '_attr',
            ));
            $result = $unserializer->unserialize($response, false);
            if (PEAR::isError($result)) {
                trigger_error('ESys_Feed_BlipTv::_fetchResponse(): '.
                    'xml data error: '.$result->getMessage(), E_USER_WARNING);
                return false;
            }
            $response = $unserializer->getUnserializedData();
        } else if ($responseType == 'json') {
            $response = preg_replace('/^blip_ws_results\(/', '', $response);
            $response = preg_replace('/\);$/', '', $response);
            //print_r($response); exit();
            $jsonParser = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
            $response = $jsonParser->decode($response);
            if (! $response) {
                trigger_error('ESys_Feed_BlipTv::_fetchResponse(): '.
                    'json data error', E_USER_WARNING);
                return false;
            }
        }
        return $response;
    }


    private function _fetchCachedResponse ($request)
    {
        exit('ESys_Feed_YouTube::_fetchCachedResponse() not yet implemented');
        // NOT YET IMPLEMENTED
        // check cache data
        // if current, return cached data
        // else get _fetchResponse(), cache it, and return it
    }

}