<?php

$dbinfo = $this->getRequired('dbinfo');
$request = $this->getRequired('request');

?>
<div class="center"><div class="content_block">

    <div class="content_title">
        <h2>Database Backup</h2>
        <p>For database <b><?php echo esc_html($dbinfo['name']); ?></b>.</p>
    </div>
    <div style="margin-bottom: 1em;">
        <span class="button"><input type="button" value="Download Backup" onclick="
            location = '<?php echo $request->url('controller'); ?>/backup';
        "></span>
    </div>
<?php 
if (count($dbinfo['tables'])) : 
?>
    <table cellspacing="0" class="record_list">
        <tr>
            <th>Table Name</th>
            <th>Records</th>
            <th>Size</th>
        </tr>
<?php
    foreach ($dbinfo['tables'] as $table) : 
?>
        <tr>
            <td><?php echo htmlentities($table['Name']); ?></td>
            <td class="number"><?php echo htmlentities($table['Rows']); ?></td>
            <td class="number"><?php echo ESys_File_Util::humanSize($table['Index_length']); ?></td>
        </tr>
<?php
    endforeach;
?>
    </table>
<?php
else :
?>
    <p>No Database Available</p>
<?php
endif;
?>
</div></div>