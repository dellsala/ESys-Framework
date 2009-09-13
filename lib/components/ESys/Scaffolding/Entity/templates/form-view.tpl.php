<?php

$entity = $this->getRequired('entity');

$primaryAttribute = $entity->primaryAttribute();

$attributeList = array();
foreach ($entity->attributeList() as $attribute) {
    if ($attribute->name() == 'id') { 
        continue;
    }
    $attributeList[] = $attribute;
}

?>
<php>

require_once 'ESys/Html/FormBuilder.php';

$request = $this->getRequired('request');
$form = $this->getRequired('form');

$formData = $form->getData();

$formBuilder = new ESys_Html_FormBuilder($formData);

$messageView = new ESys_Template('ESys/Admin/templates/message.tpl.php');

if ($form->hasErrors()) {
    $formBuilder->flagErrors($form->getErrorReport()->getFields());
    $messageText = new ESys_Template('ESys/Admin/templates/input-error-text.tpl.php');
    $messageText->set('errorMessages', $form->getErrorReport()->getMessages());
    $messageView->set('message', new ESys_Message_Warning($messageText->fetch()));
}

echo $messageView->fetch();
</php>

<div class="center"><div class="content_block">

<form class="form" action="<php> 
    echo esc_html($request->url('controller').'/save'); </php>" method="post"><div>
    <input type="hidden" name="id" value="<php> echo esc_html($formData['id']); </php>">
    <table cellspacing="0">
        <tr>
            <th>&nbsp;</th>
            <th>
                <div class="content_title">
                    <h2><?php echo $entity->displayName(); ?> Details</h2>
                </div>
            </th>
        </tr>
<?php 
foreach ($attributeList as $attribute) : 
?>
        <tr>
            <td><label class="horizontal_label"<?php if ($attribute->type() !== 'ENUM') : ?> for="<?php 
                echo $attribute->name(); ?>"<?php endif; ?>><?php 
                echo $attribute->displayName(); ?></label></td>
<?php   
    switch ($attribute->type()) : 
        case ('TEXT') :
        case ('BLOB') :
?>
            <td><div class="field"><php> $formBuilder->textarea('<?php echo $attribute->name(); ?>', 
                array('id'=>'<?php echo $attribute->name(); ?>', 'cols'=>'40', 'rows'=>'15')); </php></div></td>
<?php
        break;
        case ('ENUM') :
            $typeInfo = $attribute->typeInfo();
            $enumValues = $typeInfo['spec'];
            if ($attribute->isBoolean()) :
?>
            <td><div class="field">
                <php> $formBuilder->radio('<?php echo $attribute->name(); ?>', 'Y', array('id'=>'<?php 
                    echo $attribute->name(); ?>'); </php>Yes &nbsp;
                <php> $formBuilder->radio('<?php echo $attribute->name(); ?>', 'N'); </php>No
            </div></td>
<?php
            else :
?>
            <td><div class="field">
<?php
                $enumValueList = explode("','", trim($enumValues, "'"));
                array_walk($enumValueList, create_function('&$v', '$v = str_replace("\'\'", "\'", $v);'));
                foreach ($enumValueList as $enumValue) : 
?>
                <php> $formBuilder->radio('<?php echo $attribute->name(); ?>', '<?php 
                    echo addslashes($enumValue); ?>'); </php> <?php echo esc_html($enumValue); ?><br>
<?php               
                endforeach; 
?>
            </div></td>
<?php
            endif;
?>
<?php
        break;
        case ('DATE') :
        case ('VARCHAR') :
        case ('CHAR') :
        case ('INT') :
        default :
?>
            <td><div class="field">
                <php> $formBuilder->input('<?php 
                    echo $attribute->name(); ?>', array('class'=>'text', 'id'=>'<?php 
                    echo $attribute->name(); ?>')); </php> 
            </div></td>
<?php       
        break; 
    endswitch; 
?>
        </tr>
<?php 
endforeach; 
?>
        <tr>
            <td>&nbsp;</td>
            <td class="actions">
                    <span class="button"><input type="submit" value="Save"></span>
                    or <a href="<php> echo esc_html($request->url('controller').'/'); </php>" class="secondary_action">cancel</a>
            </td>
        </tr>
    </table>
</div></form>

</div></div>