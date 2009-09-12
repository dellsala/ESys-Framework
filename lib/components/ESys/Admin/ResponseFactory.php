<?php

require_once 'ESys/WebControl/ResponseFactory.php';
require_once 'ESys/ArrayAccessor.php';
require_once 'ESys/Message.php';


/**
 * @package ESys
 */
class ESys_Admin_ResponseFactory extends ESys_WebControl_ResponseFactory {


    /**
     * @param string
     */
    public function __construct ($packageName)
    {
        $this->packageName = $packageName;
    }


    /**
     * @param array
     * @return ESys_WebControl_Response
     */
    protected function buildNotFound ($data)
    {
        $data = new ESys_ArrayAccessor($data);
        $title = $data->get('title', 'Not found');
        $selectedMenu = $data->get('selectedMenu');
        $content = $this->createAlertContent(
            new ESys_Message_Warning(
                $data->get('content', '<b>Not Found.</b><br>The page or resource you requested was not found.')
            )
        );
        return new ESys_WebControl_Response_NotFound(
            $this->createLayout($title, $content, $selectedMenu)
        );
    }


    /**
     * @param array
     * @return ESys_WebControl_Response
     */
    protected function buildError ($data)
    {
        $data = new ESys_ArrayAccessor($data);
        $title = $data->get('title', 'Error');
        $selectedMenu = $data->get('selectedMenu');
        $content = $this->createAlertContent(
            new ESys_Message_Error(
                $data->get('content', '<b>Opps!</b><br>Sorry, and unexpected error occurred.')
            )
        );
        return new ESys_WebControl_Response_Error(
            $this->createLayout($title, $content, $selectedMenu)
        );
    }


    /**
     * @param array
     * @return ESys_WebControl_Response
     */
    protected function buildForbidden ($data)
    {
        $data = new ESys_ArrayAccessor($data);
        $request = $data->get('request');
        $auth = ESys_Application::get('authenticator');
        if (! $auth->isLoggedIn()) {
            return new ESys_WebControl_Response_Redirect(
                $request->url('frontController').'/gateway/'
            );
        }
        $title = 'Forbidden';
        $content = $this->createAlertContent(
            new ESys_Message_Error(
                $data->get('content', '<b>Forbidden!</b><br>You arenÕt allowed to access this page.')
            )
        );
        return new ESys_WebControl_Response_Forbidden(
            $this->createLayout($title, $content)
        );
    }


    /**
     * @param array
     * @return ESys_WebControl_Response
     */
    protected function buildOk ($data)
    {
        if (! array_key_exists('selectedMenu', $data)) {
            $data['selectedMenu'] = null;
        }
        return new ESys_WebControl_Response_Ok(
            $this->createLayout($data['title'], $data['content'], $data['selectedMenu'])
        );
    }


    /**
     * @param ESys_Message
     * @return string
     */
    protected function createAlertContent (ESys_Message $message)
    {
        $messageView = new ESys_Template('ESys/Admin/templates/message.tpl.php');
        $messageView->set('message', $message);
        return $messageView->fetch();
    }



    /**
     * @param string
     * @param string
     * @param string
     * @return string
     */
    protected function createLayout ($title, $content, $selectedMenu = null)
    {
        $pageView = new ESys_Template($this->packageName.'/AdminApp/templates/main.tpl.php');
        $pageView->set('content', $content);
        $pageView->set('title', $title);
        $pageView->set('selectedMenu', $selectedMenu);
        $pageView->set('request', $this->request);
        return $pageView->fetch();
    }



}