<?php
/**
 * CLI bootstrapping script. Require this file at the top of every command line script.
 */

$_SERVER['ESYS_BOOTSTRAP_LIBPATH'] = dirname(dirname(__FILE__));

ini_set('error_reporting', E_ALL | E_STRICT);

require_once $_SERVER['ESYS_BOOTSTRAP_LIBPATH'].'/library/ESys/Bootstrap.php';

ESys_Bootstrap::init();

function esys_commandline_init () {

    require_once 'ESys/CommandLine/ErrorReporter.php';
    $errorReporter = ESys_Application::get('errorReporter');
    $errorReporter->addListener(new ESys_CommandLine_ErrorReporter());

}

esys_commandline_init();