<?php


/**
 * A parsed request.
 *
 * Contains all request details organized 
 * in a format specific to the ESys_WebControl framework.
 *
 * Request Anatomy: 
 *
 * <pre>
 * /url/base  /front/controller  /to/controller / action/param1/param2 / ?var1=val1
 * 
 * |_______|  |_______________|  |____________|   |__________________|    |_______|
 * Base       Front Controller   Controller       Action Parameters       Get Data
 * Path       Path               Path
 * 
 * |__________________________________________|
 * Full Handler Path
 *
 * |__________________________|
 * Full Script Path
 *                               |___________________________________|
 *                               Query Path
 * </pre>
 *
 * @package ESys
 */
class ESys_WebControl_Request {


    protected $urlParts = array(
        'base' => '',
        'frontController' => '',
        'controller' => '',
    );

    protected $actionParameters = array();

    protected $getData = array();

    protected $postData = array();

    protected $serverData = array();
    

    /**
     * @param array $requestData
     */
    public function __construct ($requestData = array())
    {
        foreach ($requestData as $field => $value) {
            $setterMethod = 'set'.ucfirst($field);
            if (! method_exists($this, $setterMethod)) {
                trigger_error(__CLASS__.'::'.__FUNCTION__."(): invalid property {$field}",
                    E_USER_NOTICE);
                continue;
            }
            $this->$setterMethod($value);
        }
    }


    /**
     * @param string $value
     * @return void
     */
    protected function setBasePath ($value)
    {
        $value = trim($value, '/');
        $this->urlParts['base'] = empty($value) ? '' : '/'.$value;
    }


    /**
     * @param string $value
     * @return void
     */
    protected function setFrontControllerPath ($value)
    {
        $value = trim($value, '/');
        $this->urlParts['frontController'] = empty($value) ? '' : '/'.$value;
    }


    /**
     * @param string $value
     * @return void
     */
    protected function setControllerPath ($value)
    {
        $value = trim($value, '/');
        $this->urlParts['controller'] = empty($value) ? '' : '/'.$value;
    }


    /**
     * @param string|array $value
     * @return void
     */
    public function setActionParameters ($value)
    {
        if (! is_array($value)) {
            $value = trim($value, '/');
            $value = empty($value) ? array() : explode('/',$value);
        }
        $this->actionParameters = $value;
    }


    /**
     * @param array $value
     * @return void
     */
    protected function setGetData ($value)
    {
        $this->getData = is_array($value) ? $value : array();
    }


    /**
     * @param array $value
     * @return void
     */
    protected function setPostData ($value)
    {
        $this->postData = is_array($value) ? $value : array();
    }


    /**
     * @param array $value
     * @return void
     */
    protected function setServerData ($value)
    {
        $this->serverData = is_array($value) ? $value : array();
    }



    public function url ($selectedUrlPart)
    {
        if (! array_key_exists($selectedUrlPart, $this->urlParts)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."() invalid url type '{$selectedUrlPart}'",
                E_USER_ERROR);
            return null;
        }
        $url = '';
        foreach ($this->urlParts as $urlPart => $urlPartValue) {
            $url .= $urlPartValue;
            if ($urlPart == $selectedUrlPart) {
                break;
            }
        }
        return $url;
    }


    /**
     * @return string
     */
    public function actionParameters ()
    {
        return $this->actionParameters;
    }


    /**
     * @return array
     */
    public function getData ()
    {
        return $this->getData;
    }


    /**
     * @return array
     */
    public function postData ()
    {
        return $this->postData;
    }


    /**
     * @return array
     */
    public function serverData ()
    {
        return $this->serverData;
    }


}
