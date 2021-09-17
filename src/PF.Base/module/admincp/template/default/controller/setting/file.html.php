<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" class="form" action="{url link='admincp.setting.file'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='export'}</div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{_p var='product'}</label>
                <select name="export" class="form-control">
                    {foreach from=$aProducts item=aProduct}
                    <option value="{$aProduct.product_id}">{$aProduct.title}</option>
                    {/foreach}
                </select>
                {help var='admincp.setting_file_product'}
            </div>
            <div class="form-group">
                <label>{_p var='download_file_format'}</label>
                <select name="file_extension" class="form-control">
                    {foreach from=$aArchives item=aArchives}
                    <option value="{$aArchives}">.{$aArchives}</option>
                    {/foreach}
                </select>
                {help var='admincp.setting_file_extension'}
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='download'}" class="btn btn-primary" />
            </div>
        </div>
    </div>
</form>

<br />

<form method="post" class="form" action="{url link='admincp.setting.file'}" enctype="multipart/form-data">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='import'}</div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{_p var='select_file'}</label>
                <input type="file" name="import" />
                <div class="p_4">
                    {_p var='valid_file_extensions'}: {$sSupported}
                </div>
                {help var='admincp.setting_file_import'}
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='upload'}" class="btn btn-primary" />
            </div>
        </div>
    </div>
</form>