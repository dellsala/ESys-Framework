<?php

require_once 'ESys/Html/FormBuilder.php';

$pager = $this->getRequired('pager');
$urlFormat = $this->getRequired('urlFormat');

$prevPage = $pager->getPreviousPage();
$nextPage = $pager->getNextPage();

$formData = array(
    'page' => sprintf($urlFormat, $pager->getSelectedPage())
);

$form = new ESys_Html_FormBuilder($formData);
$pageOptions = array();
if ($pager->getPageCount()) {
    foreach (range(1, $pager->getPageCount()) as $i) {
        $pageOptions['page '.$i] = sprintf($urlFormat, $i);
    }
}

$selectOnChange = "location = this.options[this.selectedIndex].value;";

?>
<form action="#" method="post" class="pager" onsubmit="return false;">
<?php if ($prevPage) : ?>
    <input type="button" value="&lt;" onclick="location = '<?php
        echo addslashes(sprintf($urlFormat, $prevPage)); ?>';">
<?php else: ?>
    <input type="button" value="&lt;" disabled >
<?php endif; ?>
    <?php $form->select('page', $pageOptions, array('onchange'=>$selectOnChange)); ?> 
<?php if ($nextPage) : ?>
    <input type="button" value="&gt;" onclick="location = '<?php
        echo addslashes(sprintf($urlFormat, $nextPage)); ?>';">
<?php else : ?>
    <input type="button" value="&gt;" disabled >
<?php endif; ?>
</form>
