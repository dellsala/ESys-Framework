<?php

require_once 'ESys/Message.php';
require_once 'ESys/Http/RequestInfo.php';

$conf = ESys_Application::get('config');

$requestInfo = new ESys_Http_RequestInfo($conf->get('urlBase'), $conf->get('htdocsPath'));


$messageView = new ESys_Template('ESys/Admin/templates/message.tpl.php');
$messageView->set('message', new ESys_Message_Warning(
    '<b>400: Bad Request</b><br>'.
    'Invalid request. Check the URL and try again.<br>'.
    'Were you looking for <a href="'.$requestInfo->getScriptUrlBase().'">this</a>?'
));

$contentView = new ESys_Template('ESys/Admin/templates/module-layout.tpl.php');
$contentView->set('title', 'Error');
$contentView->set('content', $messageView);

$pageView = new ESys_Template('ESys/Admin/templates/layout.tpl.php');
$pageView->set('content', $contentView);
echo $pageView->fetch();

