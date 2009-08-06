<?php

/**
 * @package ESys
 */
class ESys_Feed_BlipTvVideo {

    private $id;
    private $data;
    
    /**
     * @param array $data
     */
    public function __construct ($data)
    {
        $this->data = $data;
        $this->id = $data['itemId'];
    }

    /**
     * @param array $options
     * @return string
     */
    public function embed ($options = array())
    {
        $defaultOptions = array(
            'width'=> $this->data['media']['width'],
            'height'=> $this->data['media']['height'],
            'autoplay'=>false,
        );
        $options = array_merge($defaultOptions, $options);
        $html = '';
		$html .= '<embed wmode="transparent" '.
		    'src="http://blip.tv/scripts/flash/blipplayer.swf'.
		        '?autoStart='.(($options['autoplay']) ? 'true' : 'false').
		        '&amp;file='.urlencode($this->data['media']['url']).'" '.
		    'quality="high" width="'.$options['width'].'" height="'.$options['height'].'" '.
		    'name="move" type="application/x-shockwave-flash" '.
		    'pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>';
		return $html;
    }

    /**
     * @return string
     */
    public function id () {
        return $this->id;
    }

    /**
     * @return string
     */
    public function title () {
        return $this->data['title'];
    }

    /**
     * @return string
     */
    public function description () {
        return strip_tags($this->data['description']);
    }

    /**
     * @return string
     */
    public function thumbnailUrl () {
        return $this->data['thumbnailUrl'];
    }

    /**
     * @return string
     */
    public function pageUrl () {
        return $this->data['url'];
    }

    /**
     * @return string
     */
    public function length () {
        $seconds = $this->data['media']['duration'];
        $minutes = floor($seconds / 60);
        $seconds = $seconds - ($minutes * 60);
        $time = sprintf('%02d:%02d', $minutes, $seconds);
        return $time;
    }

}