#!/usr/bin/php
<?php

require_once dirname(__FILE__).'/_bootstrap.php';


function esys_htaccess_fetch_candidates ($parentFolder)
{
    $candidateList = array();
    $isCandidate = false;
    $subfolders = array();
    $fileList = glob($parentFolder.'/*');
    foreach ($fileList as $file) {
        if (is_dir($file)) {
            $subfolders[] = $file;
            continue;
        }
        if (basename($file) == 'index.php') {
            $isCandidate = true;
            continue;
        }
    }
    $candidateList = array();
    if ($isCandidate) {
        $candidateList[] = $parentFolder;
    }
    foreach ($subfolders as $folder) {
        $candidateList = array_merge(
            $candidateList,
            esys_htaccess_fetch_candidates($folder)
        );
    }
    return $candidateList;
}


function esys_htaccess_write ($parentFolder)
{
    $urlBase = ESys_Application::get('config')->get('urlBase');
    $htdocs = ESys_Application::get('config')->get('htdocsPath');
    $rewriteBase = $urlBase . 
        preg_replace('/^'.preg_quote($htdocs,'/').'/', '', $parentFolder);
    ob_start();
?>
# enable mod_rewrite
RewriteEngine on

# define the base url for accessing this folder
RewriteBase <?php echo $rewriteBase; ?> 

# rewrite all requests for file and folders that do not exists
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?query=$1 [L,QSA]
<?php
    $fileContents = ob_get_clean();
    if (! $fh = fopen($parentFolder.'/.htaccess', 'w')) {
        return false;
    }
    fputs($fh, $fileContents);
    fclose($fh);
    return true;
}




$errorReporter = ESys_Application::get('errorReporter');
$errorReporter->setRealtimeReporting(true);

$htdocs = ESys_Application::get('config')->get('htdocsPath');

if (! is_dir($htdocs)) {
    echo "Error: htdocs directory {$htdocs} does not exist.";
    exit(0);
}

$candidateList = esys_htaccess_fetch_candidates($htdocs);
echo "\n";
echo count($candidateList)." candidates for .htaccess files found.\n\n";
foreach ($candidateList as $folder) {
    echo $folder."\n";
    if (file_exists($folder.'/.htaccess')) {
        echo "Note: there is already an .htaccess file \n".
            "in this folder that will be overwritten. \n";
    }
    echo "Create an .htaccess in this folder? y/n: ";
    if (strtolower(trim(fgets(STDIN))) != 'y') {
        echo "skipping...\n\n";
        continue;
    }
    if (esys_htaccess_write($folder)) {
        echo "File created.\n";
    } else {
        echo "Error. Unable to write file. Continuing...\n";
    }
    echo "\n";
}
