<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<script type="text/javascript">
{literal}
	function plugin_addFriendToSelectList()
	{
		$('#js_allow_list_input').show();
	}
{/literal}
</script>
<div class="main_break">
	{$sCreateJs}
	<form method="post" action="{url link='blog.add'}" id="core_js_blog_form" onsubmit="{$sGetJsForm}" enctype="multipart/form-data">

		{if isset($iItem) && isset($sModule)}
			<div><input type="hidden" name="val[module_id]" value="{$sModule|htmlspecialchars}" /></div>
			<div><input type="hidden" name="val[item_id]" value="{$iItem|htmlspecialchars}" /></div>
		{/if}
		<div id="js_custom_privacy_input_holder">
		{if $bIsEdit && (!isset($sModule) || empty($sModule))}
			{module name='privacy.build' privacy_item_id=$aForms.blog_id privacy_module_id='blog'}
		{/if}
		</div>
		<div><input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" /></div>

		{if $bIsEdit}
			<div><input type="hidden" name="id" value="{$aForms.blog_id}" /></div>
		{/if}

		{plugin call='blog.template_controller_add_hidden_form'}

        <div class="form-group">
            <label for="title">{required}{_p var='title'}</label>
            <input class="form-control close_warning" type="text" name="val[title]" value="{value type='input' id='title'}" id="title" size="40" />
        </div>

        {if !empty($aForms.current_image) && !empty($aForms.blog_id)}
        {module name='core.upload-form' type=blog current_photo=$aForms.current_image id=$aForms.blog_id}
        {else}
        {module name='core.upload-form' type='blog' }
        {/if}

		{plugin call='blog.template_controller_add_textarea_start'}

        <div class="form-group">
            <label for="text">{required}{_p var='post'}</label>
            {editor id='text'}
        </div>

        <div class="form-group">
            {module name='blog.add-category-list'}
        </div>

        {if Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_tag_support')}
            {module name='tag.add' sType=blog}
        {/if}

        {if !isset($sModule) || empty($sModule)}
        <div class="form-group">
            <label for="text">{_p var='control_who_can_see_this_blog'}</label>
            {if Phpfox::isModule('privacy')}
            {module name='privacy.form' privacy_name='privacy' default_privacy='blog.default_privacy_setting'}
            {/if}
        </div>
        {/if}
		
		{if Phpfox::isModule('captcha') && Phpfox::getUserParam('captcha.captcha_on_blog_add')}
            {module name='captcha.form' sType=blog}
        {/if}
		
		{plugin call='blog-template_controller_add_textarea_end'}

        {if Phpfox::getParam('core.display_required')}
        <div class="form-group">
            {required} {_p var='required_fields'}
        </div>
        {/if}

		<div class="form-group blog-add-button-group">
            {plugin call='blog.template_controller_add_submit_buttons'}

            {if $bIsEdit && $aForms.post_status == 2}
                <input type="submit" name="val[draft_update]" value="{_p var='update'}" class="btn btn-primary" />
                <input type="submit" name="val[draft_publish]" value="{_p var='publish'}" class="btn btn-default" />
            {else}
                <input type="submit" name="val[{if $bIsEdit}update{else}publish{/if}]" value="{if $bIsEdit}{_p var='update'}{else}{_p var='publish'}{/if}" class="btn btn-primary" />
            {/if}

            {if !$bIsEdit}
                <input type="submit" name="val[draft]" value="{_p var='save_as_draft'}" class="btn btn-default" />
            {/if}
            <input type="button" name="val[preview]" value="{_p var='preview'}" class="btn btn-default" onclick="tb_show('{_p var='blog_preview' phpfox_squote=true}', $.ajaxBox('blog.preview', 'height=400&amp;width=600&amp;text=' + encodeURIComponent(core_blogs_get_content('text'))), null, '', false,'POST');" />
		</div>

	</form>
</div>
