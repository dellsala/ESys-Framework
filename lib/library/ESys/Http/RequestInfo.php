<?php

/**
 * @package ESys
 */
class ESys_Http_RequestInfo
{

    private $requestUri;

    private $urlBase;

    private $htdocsPath;

    private $scriptPath;

    private $scriptUrl;

    private $queryString;


    /**
     * @param string $urlBase
     * @param string $htdocsPath
     */
    public function __construct ($urlBase, $htdocsPath)
    {

        $this->urlBase = $urlBase;
        $this->htdocsPath = $htdocsPath;
        $this->requestUri = $this->calculateRequestUri();
        $this->scriptPath = $this->calculateScriptPath();
        $this->scriptUrl = $this->calculateScriptUrl();
        $this->pathInfo = $this->calculatePathInfo();
        $this->queryString = $this->calculateQueryString();
    }



    /**
     * @return string
     */
    public function getScriptPath ()
    {
        return $this->scriptPath;
    }



    /**
     * @return string
     */
    public function getScriptUrl ()
    {
        return $this->scriptUrl;
    }



    /**
     * @return string
     */
    public function getScriptUrlBase ()
    {
        return dirname($this->getScriptUrl());
    }



    /**
     * @return string
     */
    public function getPathInfo ()
    {
        return $this->pathInfo;
    }



    /**
     * @return string
     */
    public function getQueryString ()
    {
        return $this->queryString;
    }



    /**
     * @return string
     */
    public function getRequestUri ()
    {
        return $this->requestUri;
    }



    private function calculatePathInfo ()
    {
        $patternPath = preg_quote($this->getScriptUrlBase(), '#');
        $patternFile = preg_quote(basename($this->getScriptUrl()), '#');
        $scriptUrlPattern = '#^'.$patternPath.'(/'.$patternFile.')?/?#';
        $pathInfo = preg_replace($scriptUrlPattern, '', $this->requestUri);
        $queryStringPattern = '/\?.*$/';
        $pathInfo = preg_replace($queryStringPattern, '', $pathInfo);
        return $pathInfo;
    }



    private function calculateQueryString ()
    {
        $uriParts = explode('?', $this->requestUri);
        $queryString = count($uriParts) > 1 ? $uriParts[1] : '';
        return $queryString;
    }



    private function calculateScriptUrl ()
    {
        $htdocsPattern = '/^'.preg_quote($this->htdocsPath, '/').'/';
        return preg_replace($htdocsPattern, $this->urlBase, $this->scriptPath);
        
    }


    private function calculateScriptPath ()
    {
        $backtrace = debug_backtrace();
        if (! count($backtrace)) {
            return __FILE__;
        }
        $firstCall = array_pop($backtrace);
        $path = $firstCall['file'];
        $path = str_replace("\\", "/", $path);

        return $path;
    }


    /**
     * Request Uri Normalization.
     * Implementation from Zend_Controller_Request_Http
     */
    private function calculateRequestUri ()
    {
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        } else {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                'Unable to retreive a reliable request uri value.', E_USER_WARNING);
            return null;
        }
        return $requestUri;
    }


}