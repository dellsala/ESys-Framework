<?php

$head = $this->getOptional('head');
$content = $this->getRequired('content');
$menu = $this->getRequired('menu');
$selectedMenu = $this->getOptional('selectedMenu');
$applicationTitle = $this->getRequired('applicationTitle');
$documentTitle = $this->getRequired('documentTitle');

$urlBase = App::urlBase();

ob_start();
?>
<link rel="stylesheet" type="text/css" href="<?php echo $urlBase; ?>/style/ESys/Core/reset.css">
<link rel="stylesheet" type="text/css" href="<?php echo $urlBase; ?>/style/ESys/Admin/style.css">
<?php
echo $head;
$head = ob_get_clean();


ob_start();
?>
<div class="layout_header_container">
    <div class="layout_header">
        <h1><?php echo $applicationTitle; ?></h1>
        <div class="main_menu">
            <ul>
<?php 
    foreach ($menu as $menuId => $item) :
?>
                <li<?php if ($menuId == $selectedMenu) { 
                    echo ' class="selected"'; } ?>><a href="<?php 
                    echo esc_html($item['url']); ?>"><?php echo esc_html($item['label']); ?></a></li>
<?php
    endforeach;
?> 
            </ul>
        </div>
    </div>
</div>
<div class="layout_content">
    <?php echo $content; ?>
</div>
<?php
$body = ob_get_clean();


$pageView = new ESys_Template('ESys/Core/templates/html.tpl.php');
$pageView->set('title', $documentTitle);
$pageView->set('body', $body);
$pageView->set('doctype', 'html-strict');
$pageView->set('head', $head);
echo $pageView->fetch();

