<?php

$package = $this->getRequired('package');


?>
<php>
$head = $this->getOptional('head');
$content = $this->getRequired('content');
$selectedMenu = $this->getOptional('selectedMenu');
$title = $this->getRequired('title');
$request = $this->getRequired('request');

$auth = ESys_Application::get('authenticator');

if ($auth->isLoggedIn()) {
    $menu = array(
        'login' => array(
            'label' => 'Logout',
            'url' => $request->url('frontController').'/gateway/logout',
        ),
    );
} else {
    $menu = array(
        'login' => array(
            'label' => 'Login',
            'url' => $request->url('frontController').'/gateway/',
        ),
    );
}

$applicationTitle = '<?php echo $package->base(); ?> <?php echo $package->sub(); ?>';

$pageView = new ESys_Template('ESys/Admin/templates/layout.tpl.php');
$pageView->set('documentTitle', $title.' | '.$applicationTitle);
$pageView->set('applicationTitle', $applicationTitle);
$pageView->set('content', $content);
$pageView->set('menu', $menu);
$pageView->set('selectedMenu', $selectedMenu);
$pageView->set('head', $head);
echo $pageView->fetch();

