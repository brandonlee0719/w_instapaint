<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.blog.add'}" onsubmit="$Core.onSubmitForm(this, true);">

    <div class="panel panel-default">
        <div class="panel-body">
            {if $bIsEdit}
            {if $bIsEdit && $aForms.parent_id}
            <input type="hidden" name="sub" value="{$iEditId}" />
            {else}
            <input type="hidden" name="edit" value="{$iEditId}" />
            {/if}
            <input type="hidden" name="val[name]" value="{$aForms.name}" />
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
            {field_language phrase='name' label='name' field='name' format='val[name_' size=30 maxlength=100}
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
            </div>
        </div>
    </div>


</form>
