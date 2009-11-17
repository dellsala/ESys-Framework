<?php

require_once 'ESys/WebControl/Controller.php';


class ESys_Admin_DbBackup_Controller extends ESys_WebControl_Controller {


    protected $templateDir;

    protected $mysqldumpPath;
    

    public function __construct ()
    {
        $this->templateDir = dirname(__FILE__).'/templates';
        $this->mysqldumpPath = 'mysqldump';
    }    


    protected function isAuthorized (ESys_WebControl_Request $request)
    {
        $auth = ESys_Application::get('authenticator');
        return $auth->isAuthorized();
    }


    protected function doBackup ($request)
    {
        $conf = ESys_Application::get('config');
        $command = "{$this->mysqldumpPath} ".
            "-u {$conf->get('databaseUser')} ".
            "-p{$conf->get('databasePassword')} ".
            "--opt {$conf->get('databaseName')} ";
        if (! shell_exec($this->mysqldumpPath)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.
                "(): mysqldump path '{$this->mysqldumpPath}' not found or not in PATH", E_USER_WARNING);
            return $this->getResponseFactory()->build('error', array(
                'content' => 'Unable to create backup. Check installation.',
            ));
        }
        $fileName = $conf->get('databaseName').'_'.date('Y-m-d_Hi').'.sql';
        return new ESys_Admin_DbBackup_Controller_BackupResponse(
            $command, $fileName
        );
    }


    protected function doIndex ($request)
    {
        $conf = ESys_Application::get('config');
        $dbinfo = array();
        $dbinfo['name'] = $conf->get('databaseName');
        $dbinfo['tables'] = array();

        $db = ESys_Application::get('databaseConnection');
        $result = $db->query('SHOW TABLE STATUS');
        while ($record = $result->fetch()) {
            $dbinfo['tables'][] = $record;
        }
        $mainView = new ESys_Template($this->templateDir.'/layout.tpl.php');
        $mainView->set('dbinfo', $dbinfo);
        $mainView->set('request', $request);
        return $this->getResponseFactory()->build('ok', array(
            'content' => $mainView->fetch()
        ));
    }


    protected function commonResponseData ()
    {
        return array(
            'title' => 'Database Backup',
        );
    }


}



class ESys_Admin_DbBackup_Controller_BackupResponse extends ESys_WebControl_Response_Ok {


    protected $backupCommand;


    public function __construct ($backupCommand, $fileName)
    {
        parent::__construct ('');
        $this->backupCommand = $backupCommand;
        $this->addHeader('Content-Type text/plain');
        $this->addHeader('Content-disposition: attachment; filename='.$fileName);
    }


    public function execute ()
    {
        foreach ($this->headers as $header) {
            header($header);
        }
        echo shell_exec($this->backupCommand);
    }


}