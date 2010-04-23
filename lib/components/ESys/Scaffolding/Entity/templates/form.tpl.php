<?php

$entity = $this->getRequired('entity');
$package = $this->getRequired('package');


?>
<php>


class <?php echo $package->full().'_'.ucfirst($entity->instanceName()); ?>_Form extends ESys_Form {


    public function captureInput ($rawInput)
    {
        $rawInput = new ESys_ArrayAccessor($rawInput);
        $this->data = $rawInput->get(array(
<?php
foreach ($entity->attributeList() as $attribute) :
?>
            '<?php echo $attribute->name() ?>',
<?php
endforeach;
?>
        ));
    }


    public function createValidator () 
    {
        $validator = new ESys_Validator();
<?php
foreach ($entity->attributeList() as $attribute) :
    $ruleList = $attribute->ruleFragments();
    foreach ($ruleList as $rule) :
?>
        $validator->addRule('<?php echo $attribute->name(); ?>', <?php echo $rule['ruleFragment']; ?>,
            '<?php echo addslashes($rule['message']); ?>');
<?php            
    endforeach;
endforeach;
?>
        $validator->setData($this->getData());
        return $validator;
    }


}