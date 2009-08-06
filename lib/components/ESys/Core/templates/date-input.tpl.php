<?php
/**
 * HTML form field view for a date
 *
 * Takes the following input:
 *
 * + $date: a native php DateTime object
 * + $yearList: an array of years to display in the year select list
 * + $fieldName: the name of the date field to be submitted with the form
 *
 * @see DateTime, ESys_Template
 * @package template
 * @subpackage template_basic
 */

$date = $this->getOptional('date');
$yearList = $this->getOptional('yearList');
$fieldName = $this->getOptional('fieldName', 'date');
$includeNullOptions = $this->getOptional('includeNullOptions', false);

$months = array(
   'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
   'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'
);

$selectedMonth = isset($date) ? (integer) $date->format('n') : null;
$selectedYear = isset($date) ? (integer) $date->format('Y') : null;
$selectedDay = isset($date) ? (integer) $date->format('j') : null;

if (! isset($yearList)) {
    $thisYear = (int) date('Y');
    $yearList = range($thisYear - 9, $thisYear + 10);
}
if (! is_null($selectedYear) 
    && ! in_array($selectedYear, $yearList)) 
{
    $yearList = $selectedYear < $yearList[0] 
        ? range($selectedYear - 9, $yearList[count($yearList) - 1])
        : range($yearList[0], $selectedYear + 10);
}

?>
<select name="<?php echo $fieldName; ?>_month" 
   onchange="toggleNullFields('<?php echo $fieldName; ?>', '<?php echo $fieldName; ?>_month'); update_<?php echo $fieldName; ?>_field(); " 
   id="<?php echo $fieldName; ?>_month"
>
<?php if ($includeNullOptions) :?>
    <option value="">--</option>
<?php endif; ?>
<?php
$i = 1;
foreach ($months as $month) :

?>
   <option value="<?php echo sprintf('%02d', $i); ?>" <?php
      if ($i == $selectedMonth) { echo ' selected'; } ?>><?php
      echo $month; ?></option>
<?php 

    $i++; 
endforeach; 

?>
</select>
<select name="<?php echo $fieldName; ?>_day" 
   onchange="toggleNullFields('<?php echo $fieldName; ?>', '<?php echo $fieldName; ?>_day'); update_<?php echo $fieldName; ?>_field();"
   id="<?php echo $fieldName; ?>_day"
>
<?php if ($includeNullOptions) :?>
    <option value="">--</option>
<?php endif; ?>   
<?php    

foreach (range(1, 31) as $day) : 

?>
   <option value="<?php echo sprintf('%02d', $day); ?>"<?php
      if ($day == $selectedDay) { echo ' selected'; } ?>><?php
      echo $day; ?></option>
<?php    

endforeach; 

?>
</select>
<select name="<?php echo $fieldName; ?>_year" 
   onchange="toggleNullFields('<?php echo $fieldName; ?>', '<?php echo $fieldName; ?>_year'); update_<?php echo $fieldName; ?>_field();"
   id="<?php echo $fieldName; ?>_year"
>
<?php if ($includeNullOptions) :?>
    <option value="">--</option>
<?php endif; ?>
<?php    

foreach ($yearList as $year) : 

?>
   <option value="<?php echo $year; ?>"<?php
      if ($year == $selectedYear) { echo ' selected'; } ?>><?php
      echo $year; ?></option>
<?php    

endforeach; 

?>
</select>
<input type="hidden" name="<?php 
    echo $fieldName; ?>" id="<?php 
    echo $fieldName; ?>" value="">
<script type="text/javascript">

function update_<?php echo $fieldName; ?>_field () {
    var fieldName = '<?php echo $fieldName; ?>';
    var dateParts = ['_year', '_month', '_day'];
    var datePartValues = [];
    for (var i=0; i < dateParts.length; i++) {
        var selectField = document.getElementById(fieldName+dateParts[i]);
        datePartValues.push(selectField.options[selectField.selectedIndex].value);
    }
    var dateField = document.getElementById(fieldName);
    if (document.getElementById(fieldName+'_year').value == '' || 
        document.getElementById(fieldName+'_month').value == '' || 
        document.getElementById(fieldName+'_day').value == '') 
    {
      dateField.value = '';
    }else {
      dateField.value = datePartValues.join('-');
    }
}

update_<?php echo $fieldName; ?>_field();

function toggleNullFields(fieldName, currentFieldName){

    if(document.getElementById(currentFieldName).value != '') {
        if (document.getElementById(fieldName+'_year').value == '') {
            document.getElementById(fieldName+'_year').value = <?php echo date('Y'); ?>
        }
        if (document.getElementById(fieldName+'_month').value == '') {
            document.getElementById(fieldName+'_month').value = '01'
        }
        if (document.getElementById(fieldName+'_day').value == '') {
            document.getElementById(fieldName+'_day').value = '01'
        }
    }else {
        document.getElementById(fieldName+'_year').value = '';
        document.getElementById(fieldName+'_month').value = '';
        document.getElementById(fieldName+'_day').value = '';
    }
}

</script>
