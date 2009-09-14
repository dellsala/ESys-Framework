<?php

$package = $this->getRequired('package');

?>
<php>

require_once $_SERVER['ESYS_LIBPATH'].'/components/ESys/Admin/Bootstrap.php';

ESys_Admin_Bootstrap::init('<?php echo $package->full(); ?>');

require_once 'ESys/WebControl/FrontController.php';
require_once 'ESys/Admin/ResponseFactory.php';

$frontController = new ESys_WebControl_FrontController(App::urlBase(), '/');
$frontController->setResponseFactory(
    new ESys_Admin_ResponseFactory('<?php echo $package->full(); ?>')
);
$frontController->addPath('/gateway', 'ESys_Admin_Login_Controller');

$response = $frontController->handleRequest($_GET, $_POST, $_SERVER);
$response->execute();
