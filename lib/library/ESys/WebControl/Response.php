<?php



/**
 * @package ESys
 */
class ESys_WebControl_Response {


    protected $headers = array();
    protected $body;


    /**
     * @param string
     */
    public function __construct ($body)
    {
        $this->body = $body;
    }


    /**
     * @return void
     */
    public function execute ()
    {
        foreach ($this->headers as $header) {
            header($header);
        }
        echo $this->getBody();
        exit();
    }


    /**
     * @param string
     * @return void
     */
    public function addHeader ($header)
    {
        $this->headers[] = $header;
    }


    /**
     * @return array
     */
    public function getHeaders ()
    {
        return $this->headers;
    }


    /**
     * @return string
     */
    public function getBody () 
    {
        return $this->body;
    }


}



/**
 * @package ESys
 */
class ESys_WebControl_Response_Ok extends ESys_WebControl_Response {

}



/**
 * @package ESys
 */
class ESys_WebControl_Response_NotFound extends ESys_WebControl_Response {


    /**
     * @param string
     */
    public function __construct ($body)
    {
        parent::__construct($body);
        $this->addHeader("HTTP/1.x 404 Not Found");
    }


}


/**
 * @package ESys
 */
class ESys_WebControl_Response_Redirect extends ESys_WebControl_Response {


    protected static $supportedHttpCodes = array(
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
    );


    /**
     * @param string
     * @param int
     */
    public function __construct ($location, $httpCode = 302)
    {
        parent::__construct('');
        if (! isset(self::$supportedHttpCodes[$httpCode])) {
            trigger_error(get_class($this).'::'.__FUNCTION__.
                "(): unsupported http code {$httpCode}", E_USER_ERROR);
        } else {
            $message = self::$supportedHttpCodes[$httpCode];
            $this->addHeader("HTTP/1.x {$httpCode} {$message}");
            $this->addHeader("Location: {$location}");
        }
    }


}



/**
 * @package ESys
 */
class ESys_WebControl_Response_Error extends ESys_WebControl_Response {


    /**
     * @param string
     * @param int
     */
    public function __construct ($body)
    {
        parent::__construct($body);
        $this->addHeader("HTTP/1.x 500 Internal Server Error");
    }


}



/**
 * @package ESys
 */
class ESys_WebControl_Response_Forbidden extends ESys_WebControl_Response {


    /**
     * @param string
     * @param int
     */
    public function __construct ($body)
    {
        parent::__construct($body);
        $this->addHeader("HTTP/1.x 403 Forbidden");
    }


}
