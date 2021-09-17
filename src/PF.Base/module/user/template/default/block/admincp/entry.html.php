<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<input type="hidden" name="val[title_var_name]" value="{value type='input' id=title_var_name}"/>

{field_language phrase='sTitleVarName' label='title' field='title' format='val[title][' size=40 maxlength=40 required=true label='name'}

<div style="display:none;">
	<div class="form-group">
		<label>{_p var='html_prefix'}</label>
        <input type="text" class="form-control" name="val[prefix]" value="{value type='input' id='prefix'}" size="20" maxlength="75" />
	</div>

	<div class="form-group">
		<label>{_p var='html_suffix'}</label>
        <input class="form-control" type="text" name="val[suffix]" value="{value type='input' id='suffix'}" size="20" maxlength="75" />
	</div>

	<div class="form-group">
		<label>{_p var='icon'}</label>
        {if !empty($aForms.icon_ext)}
        <div id="js_group_icon">
            <div class="p_2">
                {img server_id=$aForms.server_id title=$aForms.title alt=$aForms.title file=$aForms.icon_ext path='core.url_icon'}
            </div>
            <div class="p_4">
                <a href="#" onclick="$('#js_group_upload_icon').show(); $('#js_group_icon').hide(); return false;">Change Icon</a>
            </div>
        </div>
        {/if}
        <div id="js_group_upload_icon"{if !empty($aForms.icon_ext)} style="display:none;"{/if}>
            <input type="file" accept="image/*" name="icon" size="30" />{if !empty($aForms.icon_ext)} - <a href="#" onclick="$('#js_group_upload_icon').hide(); $('#js_group_icon').show(); return false;">{_p var='cancel'}</a>{/if}
            <div class="extra_info">
                {_p var='you_can_upload_a_jpg_gif_or_png_file'}
                <br />
                {_p var='the_advised_width_height_is_20_pixels'}
            </div>
        </div>
	</div>
</div>