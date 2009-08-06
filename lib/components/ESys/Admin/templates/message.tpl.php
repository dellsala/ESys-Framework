<?php

$message = $this->getOptional('message', null);

if (! ($message instanceof ESys_Message)) {
    return;
}

$messageStyle = array(
    'esys_message_info' => array(
        'icon' => '/style/ESys/Core/images/icon-ok.gif',
        'class' => 'info',
    ),
    'esys_message_warning' => array(
        'icon' => '/style/ESys/Core/images/icon-warning.gif',
        'class' => 'warning',
    ),
    'esys_message_error' => array(
        'icon' => '/style/ESys/Core/images/icon-error.gif',
        'class' => 'error'
    ),
);


$messageType = strtolower(get_class($message));

$icon = $messageStyle[$messageType]['icon'];
$class = $messageStyle[$messageType]['class'];

$urlBase = ESys_Application::get('config')->get('urlBase');


?>
<div class="message"><div class="messageMargin <?php echo $class; ?>">
    <img src="<?php echo $urlBase.$icon; ?>" class="icon" 
        height="32" width="32" alt="<?php echo $class; ?>">
    <div class="body">
<?php echo $message->getContent(); ?> 
    </div>
    <div class="bodyEnd"></div>
</div></div>
