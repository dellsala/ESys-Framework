#!/usr/bin/php
<?php

require_once dirname(__FILE__).'/_bootstrap.php';
App::get('errorReporter')->setRealtimeReporting(true);

require_once 'ESys/AppSkeleton/Builder.php';
require_once 'ESys/AppSkeleton/Application.php';

$argv = $_SERVER['argv'];

$applicationName = isset($argv[1]) ? $argv[1] : null;
$packageName = isset($argv[2]) ? $argv[2] : null;

if (! $applicationName || ! $packageName) {
    $syntaxError = "ERROR: Missing arguments.\n";
    $syntaxError .= "Format ->      appskeleton.php <NAME> <PACKAGE>\n";
    $syntaxError .= "Example ->     appskeleton.php \"Joe's Test App\" Joe_TestApp\n";
    fputs(STDERR, $syntaxError);
    exit(1);
}


$outputDirectory = App::libPath().'/data/appskeleton';

if (! (file_exists($outputDirectory)
    && is_writable($outputDirectory)))
{
    fputs(STDERR, "ERROR: Target directory {$outputDirectory} does not exist ".
        "or is not writable.\n\n");
    exit(1);
}


$application = new ESys_AppSkeleton_Application($applicationName, $packageName);
$builder = new ESys_AppSkeleton_Builder();
$builder->setOutputDirectory($outputDirectory);
if (! $builder->build($application)) {
    fputs(STDERR, "ERROR: Unexpected build failure.\n\n");
    exit(1);
}

echo "Skeleton built. Files can be found in\n";
echo $outputDirectory."\n\n";
exit(0);
