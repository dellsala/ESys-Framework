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
<div style="margin-bottom: 1em; text-align: right;">
    <input type="button" value="New <?php 
        echo $entity->displayName(); ?>" onclick="location = '<php>
        echo $request->url('controller').'/new'; </php>';">
</div>
<table cellspacing="0" class="item_list">
    <tr>
        <th>&nbsp;</th>
        <th><?php echo $primaryAttribute->displayName(); ?></th>
    </tr>
<php> foreach ($<?php echo $entity->instanceName(); ?>List as $i => $<?php 
    echo $entity->instanceName(); ?>) : </php>
<php>
        $editRequest = $request->url('controller').'/edit/'.$<?php echo $entity->instanceName(); ?>->get('id');
</php>
    <tr class="item <php> echo ($i % 2) ? 'even' : 'odd'; </php>">
        <td class="buttons">
            <a href="#" class="delete"  onclick="requestDelete(<php> echo $<?php 
                echo $entity->instanceName(); ?>->get('id'); </php>, '<php> 
                echo addslashes($<?php echo $entity->instanceName(); ?>->get('<?php 
                echo $primaryAttribute->name(); ?>')); </php>'); return false;" title="Delete">
                <img src="<php> echo $urlBase; </php>/style/ESys/Core/images/trash.gif" alt="Trash">
            </a>
            <a class="edit" href="<php> echo esc_html($editRequest); </php>" title="Edit">Edit</a>
        </td>
        <td>
            <h3><php> echo esc_html($<?php echo $entity->instanceName(); ?>->get('<?php 
                echo $primaryAttribute->name(); ?>')); </php></h3>
        </td>
    </tr>
<php> endforeach; </php>
</table>
<form name="deleteForm" action="<php> echo esc_html($request->url('controller').'/delete'); </php>" method="post">
    <div><input type="hidden" name="id" value=""></div>
</form>