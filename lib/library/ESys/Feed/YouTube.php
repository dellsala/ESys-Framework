<?php

require_once 'XML/Unserializer.php';
require_once 'ESys/Feed/HttpRequest.php';
require_once 'ESys/Feed/YouTubeVideo.php';

define('ESYS_FEED_YOUTUBE_URL', 'http://www.youtube.com/api2_rest');


/**
 * The YouTube.com website.
 *
 * Provides infomation about users and videos
 * availble on YouTube.com.
 *
 * @package ESys
 */
class ESys_Feed_YouTube {

    private $options;

    /**
     * @param array $options
     */
    public function __construct ($options)
    {
        $defaultOptions = array(
            'cacheDirectory' => null,
            'apiKey' => '',
        );
        $this->options = array_merge($defaultOptions, $options);
    }


    /**
     * @param string $playlistId
     * @return array Array of ESys_Feed_YouTubeVideo objects.
     */
    public function getVideosByPlaylist ($playlistId)
    {
        $url = ESYS_FEED_YOUTUBE_URL.
            '?method=youtube.videos.list_by_playlist'.
            '&dev_id='.urlencode($this->options['apiKey']).
            '&id='.urlencode($playlistId);
        $data = $this->request($url);
        if (! $data) {
            trigger_error('ESys_Feed_YouTube::getVideosByPlaylist(): '.
                'There was a problem retrieving data from YouTube.', E_USER_NOTICE);
            return false;
        }
        $videoList = array();
        foreach ($data['video_list']['video'] as $videoData) {
            $videoList[] = new ESys_Feed_YouTubeVideo($videoData);
        }
        return $videoList;
    }


    /**
     * @param string $videoId
     * @return ESys_Feed_YouTubeVideo|false
     */
    public function getVideoById ($videoId)
    {
        $url = ESYS_FEED_YOUTUBE_URL.
            '?method=youtube.videos.get_details'.
            '&dev_id='.urlencode($this->options['apiKey']).
            '&video_id='.urlencode($videoId);
        $data = $this->request($url);
        if (! $data) {
            trigger_error('ESys_Feed_YouTube::getVideoById(): '.
                'There was a problem retrieving data from YouTube.', E_USER_NOTICE);
            return false;
        }
        $data['video_details']['id'] = $videoId;
        $video = new ESys_Feed_YouTubeVideo($data['video_details']);
        return $video;
    }


    /**
     * @param string $requestUrl
     * @return array|false
     */
    public function request ($requestUrl)
    {
        if (isset($this->options['cacheDirectory'])) {
            $response = $this->_fetchCachedResponse($requestUrl);
        } else {
            $response = $this->_fetchResponse($requestUrl);
        }
        return $response;
    }


    private function _fetchResponse ($requestUrl)
    {
        $request = new ESys_Feed_HttpRequest($requestUrl);
        $request->send();
        $xml = $request->getResponse();
        if (! $xml) { return false; }
        $unserializer = new XML_Unserializer(array(
            XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE => true,
            XML_UNSERIALIZER_OPTION_ATTRIBUTES_ARRAYKEY => '_attr',
        ));
        $result = $unserializer->unserialize($xml, false);
        if (PEAR::isError($result)) {
            trigger_error('ESys_Feed_YouTube::_fetchResponse(): '.
                'xml data error: '.$result->getMessage(), E_USER_WARNING);
            return false;
        }
        return $unserializer->getUnserializedData();
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