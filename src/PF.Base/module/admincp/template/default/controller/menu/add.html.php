<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<form class="form" enctype="multipart/form-data" method="post" action="{if $bIsEdit}{url link="admincp.menu.add" id=$aForms.menu_id}{else}{url link="admincp.menu.add"}{/if}">
<div class="panel panel-default">
    <div class="panel-body">
            <input type="hidden" name="send_path" value="{url link='admincp.menu'}" />
            {if $bIsEdit}
            <input type="hidden" name="menu_id" value="{$aForms.menu_id}" />
            {/if}
            {if $bIsPage}
            <input type="hidden" name="val[page_id]" value="{$aPage.page_id}" />
            <input type="hidden" name="val[product_id]" value="{$aPage.product_id}" />
            <input type="hidden" name="val[module_id]" value="{$sModuleValue}" />
            <input type="hidden" name="val[url_value]" value="{$aPage.title_url}" />
            <input type="hidden" name="val[is_page]" value="true" />
            {/if}
            {if !$bIsPage}
            <div class="form-group"{if !PHPFOX_IS_TECHIE} style="display:none;"{/if}>
                <label for="product_id">{_p var='product'}</label>
                <select id="product_id" name="val[product_id]" class="form-control">
                    {foreach from=$aProducts item=aProduct}
                    <option value="{$aProduct.product_id}"{value type='select' id='product_id' default=$aProduct.product_id}>{$aProduct.title}</option>
                    {/foreach}
                </select>
                {help var='admincp.menu_add_product'}
            </div>
            <div class="form-group"{if !PHPFOX_IS_TECHIE} style="display:none;"{/if}>
                <label for="module_id">{_p var='module'}</label>
                <select id="module_id" name="val[module_id]" class="form-control">
                    <option value="">{_p var='select'}:</option>
                    {foreach from=$aModules key=sModule item=iModuleId}
                    <option value="{$iModuleId}|{$sModule}"{value type='select' id='module_id' default=$iModuleId}>{translate var=$sModule prefix='module'}</option>
                    {/foreach}
                </select>
                {help var='admincp.menu_add_module'}
            </div>
            {/if}
            <div class="form-group">
                <label for="m_connection" class="required">{_p var='placement'}</label>
                <select id="m_connection" name="val[m_connection]" class="form-control">
                    <option value="">{_p var='select'}:</option>
                    <optgroup label="{_p var='menu_block'}">
                        {foreach from=$aTypes item=sType}
                        <option value="{$sType}"{value type='select' id='m_connection' default=$sType}>{$sType}</option>
                        {/foreach}
                    </optgroup>
                </select>
                {help var='admincp.menu_add_connection'}
            </div>
            {if !$bIsPage}
            <div class="form-group">
                <label>{_p var='url'}</label>
                <input type="text" name="val[url_value]" id="url_value" value="{value type='input' id='url_value'}" size="40" maxlength="250" class="form-control" />
                {if !$bIsEdit && count($aPages)}
                <div class="p_4" style="display:none;">
                    {_p var='or_select_a_page'}
                    <select name="val[url_value_page]" onchange="$('#url_value').val(this.value);" class="form-control">
                        <option value="">{_p var='select'}:</option>
                        {foreach from=$aPages key=sPage item=iId}
                        <option value="{$sPage}"{value type='select' id='m_connection' default=$sType}>{$sPage}</option>
                        {/foreach}
                    </select>
                </div>
                {/if}
                {help var='admincp.menu_add_url'}
            </div>
            {/if}

            <div class="form-group">
                <label for="mobile_icon">{_p var='font_awesome_icon'}</label>
                <input id="mobile_icon" type="text" name="val[mobile_icon]" value="{value type='input' id='mobile_icon'}" class="form-control"/>
            </div>
            <div class="form-group">
                <label class="required">{_p var='menu'}</label>
                {foreach from=$aLanguages item=aLanguage}
                <div class="form-group">
                    <label>{$aLanguage.title}</label>
                    <div class="lang_value">
                        <textarea class="form-control" cols="50" rows="5" name="val[text][{$aLanguage.language_id}]">{if isset($aLanguage.text)}{$aLanguage.text|htmlspecialchars}{/if}</textarea>
                    </div>
                </div>
                {/foreach}
            </div>

            <div class="panel panel-default hide">
                <div class="panel-body">
                    {_p var='user_group_access'}
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <label>{_p var='allow_access'}</label>
                        {foreach from=$aUserGroups item=aUserGroup}
                        <div class="p_4">
                            <label><input type="checkbox" name="val[allow_access][]" value="{$aUserGroup.user_group_id}"{if isset($aAccess) && is_array($aAccess)}{if !in_array($aUserGroup.user_group_id, $aAccess)} checked="checked" {/if}{else} checked="checked" {/if}/> {$aUserGroup.title|convert|clean}</label>
                        </div>
                        {/foreach}
                        {help var='admincp.menu_add_access'}
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            {if $bIsEdit}
            <button type="submit" name="_submit" class="btn btn-primary" value="_save">{_p var="save"}</button>
            {else}
            <button type="submit" name="_submit" class="btn btn-primary" value="_save">{_p var="save"}</button>
            {/if}
        </div>
    </div>
</form>

