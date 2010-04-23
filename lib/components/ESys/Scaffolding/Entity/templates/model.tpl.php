<?php

$entity = $this->getRequired('entity');

$primaryAttribute = $entity->primaryAttribute();

$enumAttributes = array();
foreach ($entity->attributeList() as $attribute) {
    if ($attribute->type() == 'ENUM') {
        $enumAttributes[] = $attribute;
    }
}

?>
<php>


class <?php echo $entity->className(); ?> extends ESys_Data_Record {


    public function getFieldList ()
    {
        return array(
<?php
foreach ($entity->attributeList() as $attribute) :
?>
            '<?php echo $attribute->name() ?>',
<?php
endforeach;
?>
        );
    }


<?php 
if (count($enumAttributes)) :
?>
    protected function initFields()
    {
        parent::initFields();
<?php
    foreach ($enumAttributes as $attribute) :
?>
        $this->set('<?php echo $attribute->name(); ?>', '<?php 
            echo addslashes($attribute->defaultValue()); ?>');
<?php
    endforeach;
?>
    }


<?php
endif;
?>
}


class <?php echo $entity->className(); ?>_DataStore extends ESys_Data_Store_Sql {


    protected function getTableName ()
    {   
        return '<?php echo $entity->tableName(); ?>';
    }


}
