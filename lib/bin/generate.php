#!/usr/bin/php
<?php

require_once dirname(__FILE__).'/_bootstrap.php';
require_once 'ESys/ArrayAccessor.php';
require_once 'ESys/CrudBuilder/Builder.php';
require_once 'ESys/Scaffolding/Application/Generator.php';

$args = new ESys_ArrayAccessor($_SERVER['argv']);

$action = $args->get(1);


switch ($action) {


    case 'entity':
        $generator = new ESys_CrudBuilder_Builder();
        if (! $packageName = $args->get(2)) {
            echo "ERROR: missing package name argument.\n\n";
            exit(1);
        }
        if (! $tableName = $args->get(3)) {
            echo "ERROR: missing table name argument.\n\n";
            exit(1);
        }
        if (! $targetDirectory = $args->get(4)) {
            echo "ERROR: missing target directory argument.\n\n";
            exit(1);
        }
        if (! $generator->generate($packageName, $tableName, $targetDirectory)) {
            echo "ERROR: entity code generation failed. exiting.\n\n";
            exit(1);
        }
        break;


    case 'application':
        $generator = new ESys_Scaffolding_Application_Generator();
        if (! $packageName = $args->get(2)) {
            echo "ERROR: missing package name argument.\n\n";
            exit(1);
        }
        if (! $targetDirectory = $args->get(3)) {
            echo "ERROR: missing target directory argument.\n\n";
            exit(1);
        }
        $generator->generate($packageName, $targetDirectory);
        break;


    default:
        echo "ERROR: unrecognized command '{$action}'\n\n";
        exit(1);
        break;

}

exit(0);
