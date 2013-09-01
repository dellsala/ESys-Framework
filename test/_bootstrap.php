<?php

ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.
    dirname(dirname(__FILE__)).'/lib/library'.PATH_SEPARATOR.
    dirname(dirname(__FILE__)).'/lib/components'.PATH_SEPARATOR.
    dirname(__FILE__));


function esys_test_autoload($className)
{
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
	if (file_exists($fileName)) {
		require $fileName;
	}
}

spl_autoload_register('esys_test_autoload');