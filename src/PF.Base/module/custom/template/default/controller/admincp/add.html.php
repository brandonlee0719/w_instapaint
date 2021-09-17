<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !$bIsEdit}
<div id="js_group_holder" style="display:none;">
	{$sGroupCreateJs}
	<form class="form" method="post" action="{url link='admincp.custom.add'}" id="js_group_field" onsubmit="if ({$sGroupGetJsForm}) {literal}{ $(this).ajaxCall('custom.addGroup'); }{/literal} return false;">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">{_p var='group_details'}</div>
            </div>
            <div class="panel-body">
                <div {if $bIsEdit} style="display:none;"{/if} >
                    {module name='admincp.product.form' class=true}
                    {module name='admincp.module.form' class=false}
                    <div class="form-group">
                        <label for="val_type_id" class="required">{_p var='location'}</label>
                        <select name="val[type_id]" class="form-control type_id" id="val_type_id">
                            <option value="">{_p var='select'}:</option>
                            {foreach from=$aGroupTypes key=sVar item=sPhrase}
                            <option value="{$sVar}"{value type='select' id='type_id' default=$sVar}>{$sPhrase}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="val_user_group_id">{_p var='user_group'}</label>
                        <select name="val[user_group_id]" id="val_user_group_id" class="form-control">
                            <option value="">{_p var='select'}:</option>
                            {foreach from=$aUserGroups key=iKey item=aGroup}
                            <option value="{$aGroup.user_group_id}" {if $bIsEdit && $aGroup.user_group_id == $aForms.user_group_id} selected="selected"{/if}>{$aGroup.title}</option>
                            {/foreach}
                        </select>
                        <div class="help-block">
                            {_p var='select_only_if_you_want_a_specific_user_group_to_have_special_custom_fields'}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required">{_p var='group'}</label>
                        {if $bIsEdit}
                        {module name='language.admincp.form' type='text' id='group' value=$aForms.group}
                        {else}
                        {module name='language.admincp.form' type='text' id='group'}
                        {/if}
                    </div>
                </div>
                <div class="form-group">
                    <input type="submit" value="{_p var='add_group'}" class="btn btn-primary" />
                    <input type="button" value="{_p var='cancel_uppercase'}" class="btn btn-danger" id="js_cancel_new_group" />
                </div>
            </div>
        </div>

	</form>
</div>
{/if}

<div id="js_field_holder">
	{$sCustomCreateJs}
	<form class="form" method="post" action="{url link='admincp.custom.add'}" id="js_custom_field" onsubmit="{$sCustomGetJsForm}">
		{if $bIsEdit}
		<div><input type="hidden" name="id" value="{$aForms.field_id}" /></div>
		<div><input type="hidden" name="val[module_id]" value="{$aForms.module_id}"></div>
		{/if}

        <div class="panel panel-default">
            <div class="panel-body">
                <div {if $bIsEdit} style="display:none;"{/if} >
                    {module name='admincp.product.form' class=true}
                </div>
                <div class="form-group">
                <label for="js_group_listing">{_p var='group'}</label>
                <div class="form-group">
                    <select name="val[group_id]" id="js_group_listing" class="form-control">
                        {foreach from=$aGroups item=aGroup}
                        <option value="{$aGroup.group_id}"{value type='select' id='group_id' default=$aGroup.group_id}>{_p var=$aGroup.phrase_var_name}</option>
                        {/foreach}
                    </select>
                    {if !$bIsEdit && Phpfox::getUserParam('custom.can_add_custom_fields_group')}
                    <div class="table_clear_more_options"><a role="button"  class="no-ajax" id="js_create_new_group">{_p var='create_a_new_group'}</a></div>
                    {/if}
                </div>
            </div>
                <div class="form-group">
                <label for="is_required">{_p var='required'}</label>
                <div class="radio">
                    <label class="radio-inline"><input type="radio" name="val[is_required]" value="1" class="v_middle checkbox" {value type='radio' id='is_required' default='1'}/>{_p var='yes'}</label>
                    <label class="radio-inline"><input type="radio" name="val[is_required]" value="0" class="v_middle checkbox" {value type='radio' id='is_required' default='0' selected=true}/>{_p var='no'}</label>
                </div>
            </div>
                <div class="form-group">
                <label for="on_signup">{_p var='include_on_registration'}</label>
                <div class="radio">
                    <label class="radio-inline"><input type="radio" name="val[on_signup]" value="1" class="v_middle checkbox" {value type='radio' id='on_signup' default='1'}/>{_p var='yes'}</label>
                    <label class="radio-inline"><input type="radio" name="val[on_signup]" value="0" class="v_middle checkbox" {value type='radio' id='on_signup' default='0' selected=true}/>{_p var='no'}</label>
                </div>
            </div>
                <div class="form-group">
                    <label for="is_search">{_p var='include_on_search_user'}</label>
                    <div class="radio">
                        <label class="radio-inline"><input type="radio" name="val[is_search]" value="1" class="v_middle checkbox" {value type='radio' id='is_search' default='1' selected=true}/>{_p var='yes'}</label>
                        <label class="radio-inline"><input type="radio" name="val[is_search]" value="0" class="v_middle checkbox" {value type='radio' id='is_search' default='0' }/>{_p var='no'}</label>
                    </div>
                </div>
                <div {if $bShowUserGroups == false} style="display:none;"{/if}>
                    <div class="form-group">
                        <label for="user_group_id">{_p var='user_group'}</label>
                        <select id="user_group_id" name="val[user_group_id]" class="form-control">
                            <option value="">{_p var='select'}:</option>
                            {foreach from=$aUserGroups key=iKey item=aGroup}
                            <option value="{$aGroup.user_group_id}" {if $bIsEdit && $aGroup.user_group_id == $aForms.user_group_id} selected="selected"{/if}>{$aGroup.title}</option>
                            {/foreach}
                        </select>
                        <p class="help-block">{_p var='select_only_if_you_want_a_specific_user_group_to_have_special_custom_fields'}</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="type_id" class="required">{_p var='location'}</label>
                    <select id="type_id" name="val[type_id]" class="type_id form-control" required>
                        <option value="">{_p var='select'}:</option>
                        {foreach from=$aTypes key=sVar item=sPhrase}
                        <option value="{$sVar}"{value type='select' id='type_id' default=$sVar}>{$sPhrase}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group"{if $bIsEdit} style="display:none;"{/if}>
                    <label for="var_type" class="required">{_p var='type'}</label>
                    <select id="var_type" name="val[var_type]" class="var_type form-control" required>
                        <option value="">{_p var='select'}:</option>
                        <option value="textarea"{value type='select' id='var_type' default='textarea'}>{_p var='large_text_area'}</option>
                        <option value="text"{value type='select' id='var_type' default='text'}>{_p var='small_text_area_255_characters_max'}</option>
                        <option value="select"{value type='select' id='var_type' default='select'}>{_p var='selection'}</option>
                        <option value="multiselect"{value type='select' id='var_type' default='multiselect'}>{_p var='multiple_selection'}</option>
                        <option value="radio"{value type='select' id='var_type' default='radio'}>{_p var='radio'}</option>
                        <option value="checkbox"{value type='select' id='var_type' default='checkbox'}>{_p var='checkbox'}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="">{required}{_p var='name'}</label>
                    {if $bIsEdit && isset($aForms.name) && Phpfox_Locale::instance()->isPhrase('$aForms.name')}
                    {module name='language.admincp.form' type='text' id='name' mode='text' value=$aForms.name}
                    {else}
                    {if isset($aForms.name) && is_array($aForms.name)}
                    {foreach from=$aForms.name key=sPhrase item=aValues}
                    {module name='language.admincp.form' type='text' id='name' mode='text' value=$aForms.name}
                    {/foreach}
                    {else}
                    {module name='language.admincp.form' type='text' id='name' mode='text'}
                    {/if}
                    {/if}
                </div>
            </div>
        </div>
        <!-- value section -->
        <div class="panel panel-default">
            <div class="panel-body">
                {if $bIsEdit && isset($aForms.option)}
                <label>{_p var='current_values'}</label>
                <div>
                    {foreach from=$aForms.option name=options key=iKey item=aOptions}
                    <div class="table js_current_value js_option_holder" id="js_current_value_{$iKey}">
                        <span>{_p var='option_count' count=$phpfox.iteration.options}:</b> <a href="#?id={$iKey}" class="js_delete_current_option"><i class="fa fa-remove"></i></a></span>
                        <div class="form-group">
                            {module name='language.admincp.form' type='text' id='current' value=$aOptions mode='text'}
                        </div>
                    </div>
                    {/foreach}
                </div>
                {/if}
                <!--{* This next block is used as a template *}-->
                <div id="js_sample_option" style="display:none;">
                    <div class="js_option_holder">
                        <div class="form-group">
                            <span>{_p var='option_html_count'}:</b> <span class="js_option_delete"></span></span>
                            {foreach from=$aLanguages item=aLang}
                            <div>
                                <input type="text" name="val[option][#][{$aLang.language_id}][text]" value="" placeholder="{$aLang.title}" class="form-control"/>
                            </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
                <div class="_table" id="tbl_option_holder" {if !$bIsEdit} style="display:none;"{/if} >
                <label>{if $bIsEdit}{_p var="Extra Values"}{else}{_p var='values'}{/if}</label>
                <div id="js_option_holder"></div>
            </div>
            <div class="table_clear_more_options" id="tbl_add_custom_option" {if !$bIsEdit} style="display:none;"{/if} >
            <a role="button" class="js_add_custom_option">{_p var='add_new_option'}</a>
        </div>
        </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <input type="submit" value="{if $bIsEdit}{_p var='update'}{else}{_p var='add'}{/if}" class="btn btn-primary" />
                </div>
            </div>
        </div>
	</form>
</div>
