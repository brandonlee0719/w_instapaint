<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

{$sCreateJs}
<div class="panel panel-default">
    <div class="panel-body">
        <form method="post" class="form" action="{url link="admincp.product.add"}" id="js_form" onsubmit="{$sGetJsForm}">
            {if $bIsEdit}<input type="hidden" name="id" value="{$aForms.product_id}" />{/if}
            <div class="form-group">
                <label class="required">{_p var='product_id'}</label>
                {if $bIsEdit}
                <input type="hidden" name="val[product_id]" value="{value type='input' id='product_id'}" size="40" id="product_id" maxlength="25" />
                {$aForms.product_id}
                {else}
                <input class="form-control" type="text" name="val[product_id]" value="{value type='input' id='product_id'}" size="40" id="product_id" maxlength="25" />
                {/if}
            </div>
            <div class="form-group">
                <label class="required">{_p var='title'}</label>
                <input type="text" class="form-control" name="val[title]" value="{value type='input' id='title'}" size="40" id="title" maxlength="50" />
            </div>
            <div class="form-group">
                <label for="description">{_p var='description'}</label>
                <input class="form-control" type="text" name="val[description]" value="{value type='input' id='description'}" size="40" id="description" maxlength="250" />
            </div>
            <div class="form-group">
                <label class="required" for="version">{_p var='version'}</label>
                <input type="text" class="form-control" name="val[version]" value="{value type='input' id='version'}" size="10" id="version" maxlength="25" />
            </div>
            <div class="form-group">
                <label for="icon">{_p var='icon_url'}</label>
                <input type="text" name="val[icon]" value="{value type='input' id='icon'}" size="40" id="icon" maxlength="250" class="form-control"/>
            </div>
            <div class="form-group">
                <label for="vendor">{_p var='vendor'}</label>
                <input type="text" class="form-control" name="val[vendor]" value="{value type='input' id='vendor'}" size="40" id="vendor" maxlength="250" />
            </div>
            <div class="form-group">
                <label for="url">{_p var='vendor_url'}</label>
                <input type="text" class="form-control" name="val[url]" value="{value type='input' id='url'}" size="40" id="url" maxlength="250" />
            </div>
            <div class="form-group" style="display:none;">
                <label for="url_version_check">{_p var='version_check_url'}</label>
                <input class="form-control" type="text" name="val[url_version_check]" value="{value type='input' id='url_version_check'}" size="40" id="url_version_check" maxlength="250" />
            </div>

            <div class="form-group">
                <label for="is_active">{_p var='active'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active' default='1' selected='true'}/> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active' default='0'}/> {_p var='no'}</span>
                </div>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
            </div>
        </form>
    </div>
</div>

{if $bIsEdit}
<form method="post" class="form" action="{url link="admincp.product.add"}" id="js_form" onsubmit="{$sGetJsForm}">
	<div><input type="hidden" name="val[dependency][product_id]" value="{$aForms.product_id}" /></div>
	<h2>{_p var='dependencies'}</h2>
	{if isset($aDependencies) && count($aDependencies)}	
	<div class="well">
		{_p var='existing_product_dependencies'}
	</div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{_p var='dependency_type'}</th>
                    <th style="width:20%;">{_p var='compatibility_starts'}</th>
                    <th style="width:20%;">{_p var='incompatible_with'}</th>
                    <th style="width:5%;">{_p var='delete'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aDependencies key=iKey item=aDependency}
                <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                    <td>
                        {if $aDependency.type_id == 'php'}
                        PHP
                        {elseif $aDependency.type_id == 'product'}
                        Product - {$aDependency.check_id}
                        {else}
                        phpFox
                        {/if}
                    </td>
                    <td style="text-align:center;"><input class="form-control" type="text" name="val[dependency][update][{$aDependency.dependency_id}][dependency_start]" value="{$aDependency.dependency_start}" maxlength="25" size="15" /></td>
                    <td style="text-align:center;"><input class="form-control" type="text" name="val[dependency][update][{$aDependency.dependency_id}][dependency_end]" value="{$aDependency.dependency_end}" maxlength="25" size="15" /></td>
                    <td style="text-align:center;"><input type="checkbox" name="val[dependency][delete][]" class="checkbox" value="{$aDependency.dependency_id}" id="js_id_row{$aDependency.dependency_id}" /></td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>

	{/if}

    <div class="panel panel-default">
        <div class="panel-heading">
            {_p var='add_new_product_dependency'}
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{_p var='type'}</label>
                <label><input type="radio" name="val[dependency][type_id]" value="php" /> {_p var='php'}</label>
                <div style="padding-top:5px;">
                    <label><input type="radio" name="val[dependency][type_id]" value="phpfox" /> {_p var='phpfox_version'}</label>
                </div>
                <div style="padding-top:5px;">
                    <label><input type="radio" name="val[dependency][type_id]" value="product" /> {_p var='product_id'}</label> <input class="form-control" type="text" name="val[dependency][check_id]" value="" />
                </div>
            </div>
            <div class="form-group">
                <label for="dependency_start">{_p var='compatibility_starts_with_version'}</label>
                <input id="dependency_start" class="form-control" type="text" name="val[dependency][dependency_start]" value="" maxlength="25" size="15" />
            </div>
            <div class="form-group">
                <label for="dependency_end">{_p var='compatibility_end_with_version'}</label>
                <input type="text" class="form-control" name="val[dependency][dependency_end]" id="dependency_end" value="" maxlength="25" size="15" />
            </div>

            <div class="form-control">
                <input type="submit" value="{_p var='save'}" class="btn btn-primary" />
            </div>
        </div>
    </div>
</form>

<form method="post" class="form" action="{url link="admincp.product.add"}" id="js_form" onsubmit="{$sGetJsForm}">
	<div><input type="hidden" name="val[install][product_id]" value="{$aForms.product_id}" /></div>
	<h2>{_p var='install_uninstall'}</h2>
	{if isset($aInstalls) && count($aInstalls)}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='existing_install_uninstall_code'}</div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:20%;">{_p var='version'}</th>
                            <th>{_p var='install_code'}</th>
                            <th>{_p var='uninstall_code'}</th>
                            <th style="width:5%;">{_p var='delete'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$aInstalls key=iKey item=aInstall}
                        <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                            <td>
                                <input type="text" class="form-control" name="val[install][update][{$aInstall.install_id}][version]" value="{$aInstall.version}" maxlength="25" size="15" />
                            </td>
                            <td><textarea class="form-control" cols="50" rows="8" name="val[install][update][{$aInstall.install_id}][install_code]" style="width:95%;">{$aInstall.install_code|htmlspecialchars}</textarea></td>
                            <td><textarea cols="50" rows="8" name="val[install][update][{$aInstall.install_id}][uninstall_code]" style="width:95%;">{$aInstall.uninstall_code|htmlspecialchars}</textarea></td>
                            <td style="text-align:center;"><input type="checkbox" name="val[install][delete][]" class="checkbox" value="{$aInstall.install_id}" id="js_id_row{$aInstall.install_id}" /></td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
	{/if}

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='add_new_install_uninstall_code'}</div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{_p var='version'}</label>
                <input class="form-control" type="text" name="val[install][version]" value="" maxlength="25" size="15" />
            </div>
            <div class="form-group">
                <label>{_p var='install'}</label>
                <textarea class="form-control" cols="50" rows="8" name="val[install][install_code]" style="width:95%;"></textarea>
            </div>

            <div class="form-group">
                <label>{_p var='uninstall'}</div>
                <textarea cols="50" rows="8" name="val[install][uninstall_code]" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='save'}" class="btn btn-primary" />
            </div>
        </div>
    </div>
</form>
{/if}