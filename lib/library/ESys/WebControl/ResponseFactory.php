<?php

require_once 'ESys/WebControl/Response.php';

/**
 * @package ESys
 */
class ESys_WebControl_ResponseFactory {


    protected $commonData = array();


    protected $request;


    /**
     * @param array
     * @return void
     */
    public function setCommonData ($data)
    {
        $this->commonData = $data;
    }



    public function setRequest (ESys_WebControl_Request $request)
    {
        $this->request = $request;
    }


    /**
     * @param string
     * @param array
     * @return ESys_WebControl_Response
     */
    public function build ($type, $data = array())
    {
        $buildMethodName = 'build'.ucfirst($type);
        if (! method_exists($this, $buildMethodName)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.
                "(): response type {$type} not implemented.", E_USER_ERROR);
            return null;
        }
        $data = array_merge($this->commonData, $data);
        if ($this->request) {
            $data['request'] = $this->request;
        }
        return $this->$buildMethodName($data);
    }



    protected function buildNotFound ($data)
    {
        if (! array_key_exists('content', $data)) {
            $data['content'] = 'Resource not found';
        }
        return new ESys_WebControl_Response_NotFound(
            $data['content']
        );
    }


    protected function buildError ($data)
    {
        if (! array_key_exists('content', $data)) {
            $data['content'] = 'Unexpected error';
        }
        return new ESys_WebControl_Response_Error(
            $data['content']
        );
    }


    protected function buildForbidden ($data)
    {
        if (! array_key_exists('content', $data)) {
            $data['content'] = 'Forbidden';
        }
        return new ESys_WebControl_Response_Forbidden(
            $data['content']
        );
    }


    protected function buildOk ($data)
    {
        return new ESys_WebControl_Response_Ok(
            $data['content']
        );
    }


    protected function buildRedirect ($data)
    {
        if (! array_key_exists('code', $data)) {
            $data['code'] = 302;
        }
        return new ESys_WebControl_Response_Redirect(
            $data['url'],
            $data['code']
        );
    }



}