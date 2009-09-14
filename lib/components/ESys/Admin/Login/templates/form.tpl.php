<?php

require_once 'ESys/Html/FormBuilder.php';

$request = $this->getRequired('request');
$form = $this->getRequired('form');

$formBuilder = new ESys_Html_FormBuilder($form->getData());

if ($form->hasLoginError()) {
    $messageView = new ESys_Template('ESys/Admin/templates/message.tpl.php');
    $messageView->set('message', new ESys_Message_Warning(
        '<b>Login Error</b><br>'.
        'Unrecognized username/password combination'
    ));
    echo $messageView->fetch();
}

?>
<div class="center"><div class="content_block">

<form class="form" action="<?php echo $request->url('controller'); ?>/login" method="post"><div>
    <table cellspacing="0">
        <tr>
            <td><label class="horizontal_label" for="username">Username</label></td>
            <td><div class="field"><?php 
                $formBuilder->input('username', array('class'=>'text','id'=>'username')); 
                ?></div></td>
        </tr>
        <tr>
            <td><label class="horizontal_label" for="password">Password</label></td>
            <td><div class="field"><?php 
                $formBuilder->password('password', array('class'=>'text','id'=>'password')); 
                ?></div></td>
        </tr>
        <tr class="actions">
            <td>&nbsp;</td>
            <td><span class="button"><input type="submit" value="Login"></span></td>
        </tr>
    </table>
</div></form>
</div></div>