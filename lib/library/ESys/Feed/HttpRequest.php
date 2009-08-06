<?php

/**
 * @package ESys
 */
class ESys_Feed_HttpRequest {

    private $url;
    private $response;


    /**
     * @param string $url
     */
    public function __construct ($url)
    {
        $this->url = $url;
    }


    /**
     * @return boolean
     */
    public function send ()
    {
        $url = $this->url;
        if (ini_get('allow_url_fopen')) {
            $fileArray = @file($url);
            if ($fileArray) {
                $response = implode('', $fileArray);
            } else {
                $response = false;
            }
        } else if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $response = curl_exec($ch);
        } else if ((include_once "HTTP/Request.php") 
            && class_exists('HTTP_Request'))
        {
            $req = new HTTP_Request($url);
            if (!PEAR::isError($req->sendRequest())) {
                $response = $req->getResponseBody();
            } else {
                $response = false;
            }
        } else {
            trigger_error('ESys_Feed_HttpRequest::send(): '.
                'http requests are not supported on this server', E_USER_WARNING);
            return false;
        }
        if ($response === false) {
            trigger_error('ESys_Feed_HttpRequest::send(): '.
                'http request failed due to a network error.', E_USER_WARNING);
            return false;
        }
        $this->response = $response;
        return true;
    }


    /**
     * @return string
     */
    public function getResponse ()
    {
        return $this->response;
    }


}


?>