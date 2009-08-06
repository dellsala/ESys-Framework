<?php

$request = $this->getRequired('request');
$message = $this->getRequired('message');
$redirectUrl = $this->getOptional('redirectUrl');

if (! isset($redirectUrl)) {
    $redirectUrl = $request->getFullHandlerPath();
}

$messageView = new ESys_Template('ESys/Admin/templates/message.tpl.php');
$messageView->set('message', $message);

echo $messageView->fetch();

?>
<input type="submit" value="Continue" onclick="location = '<?php
    echo addslashes($redirectUrl); ?>';">
