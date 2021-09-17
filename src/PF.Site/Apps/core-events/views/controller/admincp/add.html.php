<?php 
/**
 * [PHPFOX_HEADER]
 *
 */
 
defined('PHPFOX') or exit('NO DICE!'); 
?>

<form method="post" action="{if $bIsEdit}{url link='admincp.event.add' id=$iEditId}{else}{url link='admincp.event.add'}{/if}"  onsubmit="$Core.onSubmitForm(this, true);">
    {if $bIsEdit}
	<input type="hidden" name="val[edit_id]" value="{$iEditId}" />
	<input type="hidden" name="val[name]" value="{$aForms.name}" />
    {/if}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='event_category_details'}
            </div>
        </div>

        <div class="panel-body">
            {if !isset($aForms) || !empty($aForms.parent_id)}
            <div class="form-group">
                <label>{_p var='parent_category'}</label>
                <select name="val[parent_id]" class="form-control">
                    <option value="0">{_p var='select'}:</option>
                    {foreach from=$aParentCategories item=aParentCategory}
                    <option {if $bIsEdit && $aForms.parent_id==$aParentCategory.category_id}selected{/if} value="{$aParentCategory.category_id}">{softPhrase var=$aParentCategory.name}</option>
                    {/foreach}
                </select>
            </div>
            {/if}

            <div class="form-group">
                <label for="name">{required}{_p var='name'}:</label>
                {field_language phrase='name' label='Title' field='name' format='val[name_' size=30 maxlength=255}
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">{_p var='submit'}</button>
        </div>
    </div>
</form>