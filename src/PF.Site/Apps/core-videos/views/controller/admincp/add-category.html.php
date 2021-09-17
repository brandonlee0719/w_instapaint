<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-body">
        <form method="post" action="{url link='admincp.v.add-category'}" onsubmit="$Core.onSubmitForm(this, true);">
        {if $bIsEdit}
            {if $bIsEdit && $aForms.parent_id}
                <div><input type="hidden" name="sub" value="{$iEditId}" /></div>
            {else}
                <div><input type="hidden" name="edit" value="{$iEditId}" /></div>
            {/if}
            <div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
        {/if}
        {if $bIsEdit && !$aForms.parent_id}{else}
            <div class="form-group">
                <label>{_p('parent_category')}</label>
                <select name="val[parent_id]" id="add_select" class="form-control">
                    {if !$bIsEdit}
                        <option value="0">{_p('None')}</option>
                    {/if}
                    {foreach from=$aCategories item=aCategory}
                        <option {if isset($aForms.parent_id) && $aCategory.category_id == $aForms.parent_id}selected="true"{/if} value="{$aCategory.category_id}"{value type='select' id='parent_id' default=$aCategory.category_id}>
                            {softPhrase var=$aCategory.name|convert}
                        </option>
                    {/foreach}
                </select>
            </div>
        {/if}
        {foreach from=$aLanguages item=aLanguage}
            <div class="form-group">
                <label>{_p('name_in')}&nbsp;<strong>{$aLanguage.title}</strong>:</label>
                {assign var='value_name' value="name_"$aLanguage.language_id}
                <input class="form-control" type="text" name="val[name_{$aLanguage.language_id}]" value="{value id=$value_name type='input'}" size="30" />
                <p class="help-block">
                    {if $aLanguage.is_default}
                        {_p var='default_language_can_not_be_empty'}
                    {else}
                        {_p var='if_the_category_is_empty_then_its_value_will_have_the_same_value_as_default_language'}
                    {/if}
                </p>
            </div>
        {/foreach}

        <div class="form-group">
            <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
        </div>
    </form>
    </div>
</div>