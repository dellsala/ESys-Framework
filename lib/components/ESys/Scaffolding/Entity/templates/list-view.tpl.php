<?php

$entity = $this->getRequired('entity');

$primaryAttribute = $entity->primaryAttribute();

?>
<php>

$request = $this->getRequired('request');
$<?php echo $entity->instanceName(); ?>List = $this->getRequired('<?php 
    echo $entity->instanceName(); ?>List');

$urlBase = $request->url('base');

</php>
<script type="text/javascript">
    var requestDelete = function (id, name) {
        if (! confirm('Are you sure you want to delete record "'+name+'"?')) {
            return;
       }
        var form = document.forms['deleteForm'];
        form.elements['id'].value = id;
        form.submit();
    }
</script>
<form name="deleteForm" action="<php> 
    echo esc_html($request->url('controller').'/delete'); </php>" method="post">
    <div><input type="hidden" name="id" value=""></div>
</form>

<div class="center"><div class="content_block">

<span class="button" style="float:right;">
    <input type="button" value="New <?php 
        echo $entity->displayName(); ?>" onclick="location = '<php>
        echo $request->url('controller').'/new'; </php>';">
</span>

<div class="content_title">
    <h2><?php echo $entity->displayName(); ?> List</h2>
    <p>Create, update and delete <?php echo $entity->displayName(); ?> records.</p>
</div>

<table cellspacing="0" class="record_list">
    <tr>
        <th style="width:50px;">&nbsp;</th>
        <th><?php echo $primaryAttribute->displayName(); ?></th>
    </tr>
<php> 
foreach ($<?php echo $entity->instanceName(); ?>List as $i => $<?php echo $entity->instanceName(); ?>) : 
    $editRequest = $request->url('controller').'/edit/'.$<?php echo $entity->instanceName(); ?>->getId();
</php>
    <tr class="<php> echo ($i % 2) ? 'even' : 'odd'; </php>">
        <td class="actions">
            <a href="#" class="delete"  onclick="requestDelete(<php> echo $<?php 
                echo $entity->instanceName(); ?>->getId(); </php>, '<php> 
                echo addslashes($<?php echo $entity->instanceName(); ?>->get('<?php 
                echo $primaryAttribute->name(); ?>')); </php>'); return false;" title="Delete">
                <img src="<php> echo $urlBase; </php>/style/ESys/Admin/images/trash.gif" alt="Trash">
            </a>
            <a class="edit" href="<php> echo esc_html($editRequest); </php>" title="Edit">Edit</a>
        </td>
        <td>
            <h3><php> echo esc_html($<?php echo $entity->instanceName(); ?>->get('<?php 
                echo $primaryAttribute->name(); ?>')); </php></h3>
        </td>
    </tr>
<php> 
endforeach; 
</php>
</table>

</div></div>
