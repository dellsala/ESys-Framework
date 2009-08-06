<?php

/**
 * @package ESys
 */
class ESys_Feed_YouTubeVideo {

    private $id;
    private $data;
    
    /**
     * @param array $data
     */
    public function __construct ($data)
    {
        $this->data = $data;
        $this->id = $data['id'];
    }

    /**
     * @param array $options
     * @return string
     */
    public function embed ($options = array())
    {
        $defaultOptions = array(
            'width'=>450,
            'height'=>370,
            'autoplay'=>false,
        );
        $options = array_merge($defaultOptions, $options);
        $html = '';
		$html .= '<object width="'.$options['width'].'" height="'.$options['height'].'">';
		$html .= '<param name="movie" value="http://www.youtube.com/v/'.$this->id.
		    (($options['autoplay']) ? '&autoplay=1' : '').'"/>';
		$html .= '<param name="wmode" value="transparent"/>';
		$html .= '<embed src="http://www.youtube.com/v/'.$this->id.
		    (($options['autoplay']) ? '&autoplay=1' : '').'" '.
		    'type="application/x-shockwave-flash" wmode="transparent" '.
		    'width="'.$options['width'].'" height="'.$options['height'].'"/>';
		$html .= '</object>';
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
        return $this->data['description'];
    }

    /**
     * @return string
     */
    public function thumbnailUrl () {
        return $this->data['thumbnail_url'];
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
        $seconds = $this->data['length_seconds'];
        $minutes = floor($seconds / 60);
        $seconds = $seconds - ($minutes * 60);
        $time = sprintf('%0d:%0d', $minutes, $seconds);
        return $time;
    }

}