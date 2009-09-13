<?php

require_once 'ESys/WebControl/Controller.php';
require_once 'ESys/Admin/Login/Form.php';

class ESys_Admin_Login_Controller extends ESys_WebControl_Controller {


    protected function getForm ()
    {
        return new ESys_Admin_Login_Form();
    }


    public function doIndex ($request)
    {
        $loginForm = $this->getForm();
        return $this->getResponseFactory()->build('ok', array(
            'content' => $loginForm->render($request)
        ));
    }


    public function doLogin ($request)
    {
        $auth = ESys_Application::get('authenticator');
        $loginForm = $this->getForm();
        $loginForm->captureInput($request->postData());
        if (! $auth->login($loginForm->getData())) {
            $loginForm->hasLoginError(true);
            return $this->getResponseFactory()->build('ok', array(
                'content' => $loginForm->render($request)
            ));
        }
        return new ESys_WebControl_Response_Redirect(
            $request->url('frontController').'/'
        );
    }


    public function doLogout ($request)
    {
        $auth = ESys_Application::get('authenticator');
        $auth->logout();
        return new ESys_WebControl_Response_Redirect(
            $request->url('controller').'/'
        );
    }


    public function commonResponseData ()
    {
        return array(
            'title' => 'Login',
            'selectedMenu' => 'login'
        );
    }


}