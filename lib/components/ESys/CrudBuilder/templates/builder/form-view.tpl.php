<?php

$entity = isset($entity) ? $entity : null;

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
<form action="<php> echo esc_html($request->url('controller').'/save'); </php>" method="post">
<div>
<input type="hidden" name="id" value="<php> echo esc_html($formData['id']); </php>">
<table class="form_layout">
<?php foreach ($attributeList as $attribute) : ?>
    <tr>
        <td class="label"><?php echo $attribute->displayName(); ?></td>
<?php   switch ($attribute->type()) : ?>
<?php
            case ('TEXT') :
            case ('BLOB') :
?>
        <td><php> $formBuilder->textarea('<?php echo $attribute->name(); ?>', 
            array('cols'=>'40', 'rows'=>'15')); </php></td>
<?php
            break;
            case ('ENUM') :
                $typeInfo = $attribute->typeInfo();
                $enumValues = $typeInfo['spec'];
                if ($attribute->isBoolean()) :
?>
        <td>
            <php> $formBuilder->radio('<?php echo $attribute->name(); ?>', 'Y'); </php>Yes &nbsp;
            <php> $formBuilder->radio('<?php echo $attribute->name(); ?>', 'N'); </php>No
        </td>
<?php
                else :
?>
        <td>
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
        </td>
<?php
                endif;
?>
<?php
            break;
            case ('DATE') :
?>
        <td>
<php> 
                $date = new DateTime($<?php echo $entity->instanceName(); ?>->get('<?php echo $attribute->name(); ?>'));
                $dateInputView = new ESys_Template('ESys/Core/templates/date-input.tpl.php');
                $dateInputView->set('date', $date);
                $dateInputView->set('fieldName', '<?php echo $attribute->name(); ?>');
                echo $dateInputView->fetch();
</php>
        </td>
<?php
            break;
            case ('VARCHAR') :
            case ('CHAR') :
            case ('INT') :
            default :
?>
        <td><php> $formBuilder->input('<?php echo $attribute->name(); ?>', array('size'=>'40')); </php></td>
<?php       break; ?>
<?php   endswitch; ?>
    </tr>
<?php endforeach; ?>
    <tr>
        <td>&nbsp;</td>
        <td>
            <input type="submit" value="Save">
            or <a href="<php> echo esc_html($request->url('controller').'/'); </php>">cancel</a>
        </td>
    </tr>
</table>
</div>
</form>
