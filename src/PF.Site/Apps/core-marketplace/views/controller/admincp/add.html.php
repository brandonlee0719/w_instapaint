<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bIsEdit}
<form method="post" action="{url link='admincp.marketplace.add' id=$iEditId}" onsubmit="$Core.onSubmitForm(this, true);">
    <div><input type="hidden" name="val[edit_id]" value="{$iEditId}" /></div>
    <div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
{else}
<form method="post" action="{url link='admincp.marketplace.add'}" onsubmit="$Core.onSubmitForm(this, true);">
{/if}
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
                <label>
                    {_p var='parent_category'}:
                </label>
                <select name="val[parent_id]" class="form-control">
                    <option value="0">{_p var='select'}:</option>
                    {foreach from=$aParentCategories item=aParentCategory}
                    <option {if $bIsEdit && $aForms.parent_id==$aParentCategory.category_id}selected{/if} value="{$aParentCategory.category_id}">{softPhrase var=$aParentCategory.name}</option>
                    {/foreach}
                </select>
            </div>
            {field_language phrase='name' label='name' field='name' format='val[name_' size=30 maxlength=100 required=true}
            <div class="panel-footer">
                <div class="form-group">
                    <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
                </div>
            </div>
        </div>
    </div>
</form>