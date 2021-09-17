<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-info">
    <div class="panel-heading">
        <div class="panel-title">{$product.title|clean}</div>
    </div>
    <div class="panel-body">
        <a class="btn btn-block btn-danger" href="{url link='admincp.product.file' install=$product.product_id}">{_p var='install'}</a>
    </div>
</div>