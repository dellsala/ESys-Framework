<?php

$packageName = $this->getRequired('packageName');


?>
<php>
$head = $this->getOptional('head');
$content = $this->getRequired('content');
$selectedMenu = $this->getOptional('selectedMenu');
$title = $this->getRequired('title');

$auth = ESys_Application::get('authenticator');

$urlBase = ESys_Application::get('config')->get('urlBase');

if ($auth->isLoggedIn()) {
    $menu = array(
        'login' => array(
            'label' => 'Logout',
            'url' => $urlBase.'/gateway/logout',
        ),
    );
} else {
    $menu = array(
        'login' => array(
            'label' => 'Login',
            'url' => $urlBase.'/gateway/',
        ),
    );
}

$applicationTitle = '<?php echo $packageName; ?> Admin';

$pageView = new ESys_Template('ESys/Admin/templates/layout.tpl.php');
$pageView->set('documentTitle', $title.' | '.$applicationTitle);
$pageView->set('applicationTitle', $applicationTitle);
$pageView->set('content', $content);
$pageView->set('menu', $menu);
$pageView->set('selectedMenu', $selectedMenu);
$pageView->set('head', $head);
echo $pageView->fetch();

