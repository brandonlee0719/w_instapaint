<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.groups.add-category'}" enctype="multipart/form-data">
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='add_new_category'}</div>
    </div>
    <div class="panel-body">
            {if $bIsEdit}
            {if $bIsEdit && isset($aForms.category_id)}
            <div><input type="hidden" name="sub" value="{$iEditId}" /></div>
            {else}
            <div><input type="hidden" name="category_id" value="{$iEditId}" /></div>
            {/if}
            <div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
            {/if}
            {if $bIsEdit && !isset($aForms.category_id)}{else}
            <div class="form-group">
                <label for="add_select">{_p('Parent category')}</label>
                <select name="val[type_id]" id="add_select" class="form-control" autofocus>
                    {if !$bIsEdit}
                    <option value="0">{_p('None')}</option>
                    {/if}
                    {foreach from=$aTypes item=aType}
                    <option value="{$aType.type_id}" {value type='select' id='type_id' default=$aType.type_id}>
                        {if Phpfox::isPhrase($this->_aVars['aType']['name'])}
                        {_p var=$aType.name}
                        {else}
                        {$aType.name|convert}
                        {/if}
                    </option>
                    {/foreach}
                </select>
                {foreach from=$aTypes item=aType}
                <div class="hidden" id="pages_type_{$aType.type_id}" data-group="1"></div>
                {/foreach}
            </div>
            {/if}

            {field_language phrase='name' label='name' field='name' format='val[name_' size=30 maxlength=100}

            {if !$bIsEdit || ($bIsEdit && !$bIsSub)}
            <div class="form-group" id="image_select">
                <label for="image">{_p var='image'}</label>
                {if isset($aForms.image_path) && $aForms.image_path}
                <div class="category-image">
                    <a href="{img server_id=$aForms.image_server_id path='core.path_actual' file=$aForms.image_path return_url=true}" class="thickbox">
                        {img server_id=$aForms.image_server_id path='core.path_actual' file=$aForms.image_path suffix='_200' max_width='200' max_height='200'}
                    </a>
                    <a class="btn btn-danger" role="button" onclick="$Core.jsConfirm({l}message:oTranslations['are_you_sure_you_want_to_delete_this_category_image']{r}, function(){l}$.ajaxCall('groups.deleteCategoryImage', 'type_id={$aForms.type_id}'){r}, function(){l}{r})"><i class="fa fa-trash"></i></a>
                </div>
                <div style="clear: both;"></div>
                {/if}
                <input type="file" name="image" id="image" accept="image/*" class="form-control">
                <p class="help-block">
                    {_p var='upload_image_for_category'}
                </p>
            </div>
            {/if}
    </div>
    <div class="panel-footer">
        <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
    </div>
</div>
</form>

{if !$bIsEdit}
{literal}
<script type="text/javascript">
    document.getElementById('add_select').onchange = function () {
        if (this.value != 0) {
            document.getElementById('image_select').style.display = 'none';
        } else {
            document.getElementById('image_select').style.display = 'block';
        }
    };
</script>
{/literal}
{/if}