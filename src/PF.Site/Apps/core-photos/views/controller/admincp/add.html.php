<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.photo.add'}" onsubmit="$Core.onSubmitForm(this, true);">
    <div class="panel panel-default">
        <div class="panel-body">
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
                            <option value="{$aCategory.category_id}"{value type='select' id='parent_id' default=$aCategory.category_id}>
                                {softPhrase var=$aCategory.name|convert}
                            </option>
                        {/foreach}
                    </select>
                </div>
            {/if}
            {field_language phrase='name' label='name' field='name' format='val[name_' size=30 maxlength=100}
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
        </div>
    </div>
</form>