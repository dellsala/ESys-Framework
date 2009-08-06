<?php
/**
 * Default HTML Document Template
 *
 * This template will render errors in the $_errors global variable.
 * This is an object of the class ESys_ErrorStack
 *
 * Accepts the following Input
 *
 * + $title:   The title of the document
 * + $head:    Any addtional head content
 * + $body:    The body of the document
 * + $doctype: The doctype to use ('html-trans'|'html-strict'|'xhtml-trans')
 * + $charset: The character set to declare. ('utf-8'|'iso-8859-1'|...)
 *
 * @package template
 * @subpackage basic
 * @see ESys_Template, ESys_ErrorReporter
 */

$config = ESys_Application::get('config');
$errorReporter = ESys_Application::get('errorReporter');

$title = $this->getOptional('title', 'Untitled');
$head = $this->getOptional('head');
$body = $this->getOptional('body', 'No Content.');
$doctype = $this->getOptional('doctype', 'html-strict');
$charset = $this->getOptional('charset', 'utf-8');

?>
<?php if ($doctype == 'html-trans') : ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<?php elseif ($doctype = 'html-strict') : ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php elseif ($doctype = 'xhtml-trans') : ?>
<?php echo '<?xml version="1.0" encoding="'.$charset.'"?>'; ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php endif; ?>
<head>
    <meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>">
    <meta http-equiv="imagetoolbar" content="no">
    <title><?php echo $title; ?></title>
<?php if (isset($head)) { echo $head; } ?>
</head>
<body>
<?php echo $body; ?> 
<?php if ($config->get('displayErrors') && $errorReporter->hasErrors()) : ?>
   <pre class="errors">
<?php echo $errorReporter->report(); ?>
   </pre>
<?php endif; ?>
</body>
</html>
