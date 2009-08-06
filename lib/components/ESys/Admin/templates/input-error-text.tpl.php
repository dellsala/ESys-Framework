<?php

$errorMessages = $this->getRequired('errorMessages');

?>
<b>Input Error:</b><br>
<?php
foreach ($errorMessages as $message) {
    echo $message."<br>\n";
}
