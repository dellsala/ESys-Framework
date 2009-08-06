<?php

$appTitle = $this->getOptional('appTitle', 'Admin');
$pageTitle = $this->getOptional('pageTitle', 'Untitled');
$menu = $this->getOptional('menu', array());
$head = $this->getOptional('head');
$content = $this->getOptional('content', '_NO_CONTENT_');

$urlBase = ESys_Application::get('config')->get('urlBase');


ob_start();
?>
<link rel="stylesheet" type="text/css" href="<?php echo $urlBase; ?>/style/ESys/Core/default.css">
<link rel="stylesheet" type="text/css" href="<?php echo $urlBase; ?>/style/ESys/Admin/layout.css">
<link rel="stylesheet" type="text/css" href="<?php echo $urlBase; ?>/style/ESys/Core/form-layout.css">
<?php
echo $head;
$head = ob_get_clean();


ob_start();
?>
<div id="header_container">
    <div id="header">
        <h1><?php echo $appTitle; ?></h1>
        <div id="header_menu">
<?php 
    $menuHtml = array();
    foreach ($menu as $label => $url) {
        $menuHtml[] = '            <a href="'.htmlentities($url).'">'.htmlentities($label).'</a>';
    }
    echo implode(" | \n", $menuHtml);
?> 
        </div>
    </div>
</div>
<div id="module_container">
    <div id="module_header">
        <h1><?php echo $pageTitle; ?></h1>
    </div>
    <div id="module_content">
        <?php echo $content; ?>
    </div>
</div>
<?php
$body = ob_get_clean();


$pageView = new ESys_Template('ESys/Core/templates/html.tpl.php');
$pageView->set('title', $appTitle.' :: '.$pageTitle);
$pageView->set('body', $body);
$pageView->set('doctype', 'html-strict');
$pageView->set('head', $head);
echo $pageView->fetch();

