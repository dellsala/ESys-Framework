<?php

$message = $this->getOptional('message', null);

if (! ($message instanceof ESys_Message)) {
    return;
}

$messageStyle = array(
    'esys_message_info' => array(
        'icon' => 'icon-ok.gif',
        'class' => 'info',
    ),
    'esys_message_warning' => array(
        'icon' => 'icon-warning.gif',
        'class' => 'warning',
    ),
    'esys_message_error' => array(
        'icon' => 'icon-error.gif',
        'class' => 'error'
    ),
);


$messageType = strtolower(get_class($message));

$icon = $messageStyle[$messageType]['icon'];
$class = $messageStyle[$messageType]['class'];

$urlBase = ESys_Application::get('config')->get('urlBase');


?>
<div class="message message_<?php echo $class; ?>">
    <img src="<?php echo $urlBase.'/style/ESys/Admin/images/'.$icon; ?>" class="icon" 
        height="32" width="32" alt="<?php echo $class; ?>">
    <div class="body">
<?php echo $message->getContent(); ?> 
    </div>
    <div class="footer"></div>
</div>
