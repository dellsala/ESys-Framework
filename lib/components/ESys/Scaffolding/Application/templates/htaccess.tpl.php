<?php

?>
# enable mod_rewrite
RewriteEngine on

# define the base url for accessing this folder
RewriteBase /

# rewrite all requests for file and folders that do not exists
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?query=$1 [L,QSA]


