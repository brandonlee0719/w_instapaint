<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Attachment
 * @version 		$Id: upload.html.php 6589 2013-09-05 12:27:50Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bIsAllowed}
<div class="js_upload_attachment_parent_holder">
    {add_script key='share.js' value='module_attachment'}
	<div class="js_default_upload_form p_bottom_4" id="js_new_temp_form_0_{$sCategoryId}">
		<div class="js_upload_form_holder_global_temp">
	
		</div>
		<div class="js_upload_form_holder_global">
			<div class="js_upload_form_holder">
				<form method="post" enctype="multipart/form-data" action="{url link='attachment.frame'}?textarea_id={$id}" target="js_upload_frame" id="attachment_js_upload_frame_form" class="js_upload_frame_form">
					<div><input type="hidden" name="category_name" value="{$sCategoryId}" class="category_name" /></div>
					<div><input type="hidden" name="input" value="{$sAttachmentInput}" /></div>
					<div><input type="hidden" name="attachment_obj_id" value="{$sAttachmentObjId}" /></div>
					<div><input type="hidden" name="upload_id" value="js_new_temp_form_0_{$sCategoryId}" class="js_temp_upload_id" /></div>
					{if $bIsAttachmentInline}
					<div><input type="hidden" name="attachment_inline" value="true" /></div>
					{/if}								
					{if $sCustomAttachment}
					<div><input type="hidden" name="custom_attachment" value="{$sCustomAttachment}" /></div>
					{/if}
					<input {if isset($attachment_custom) && $attachment_custom = 'photo'} accept="image/*"{/if} class="js_file_attachment" type="file" name="file[]" value="" onchange="$Core.uploadNewAttachment(this, {if $sCustomAttachment == 'video'}false{else}true{/if}, '{_p var='uploading' phpfox_squote=true}');" />
					<iframe name="js_upload_frame" height="500" width="500" frameborder="1" style="display:none;"></iframe>
					<div class="extra_info">
						<b>{_p var='valid_file_extensions'}:</b>
						{if empty($sAttachmentInput) && empty($sCustomAttachment)}
                            {if !$aValidExtensions}{_p var='none'}{/if}
							{foreach from=$aValidExtensions key=iKey item=sValidExtension}
                                {if $iKey != 0},{/if} {$sValidExtension}
                            {/foreach}
						{else}
							{if $sCustomAttachment == 'photo'}
								{_p var='jpg_gif_or_png'}
							{elseif $sCustomAttachment == 'video'}
								{$sVideoFileExt}
							{else}
								{_p var='jpg_gif_or_png'}
							{/if}
						{/if}
						{if $iMaxFileSize !== null}
						<br />
						{_p var='the_file_size_limit_is_max_file_size' max_file_size=$iMaxFileSize}
						{/if}
					</div>
				</form>
			</div>
			<div class="js_upload_form_image_holder">
				<div class="js_upload_form_image_holder_image">{img theme='ajax/add.gif'}</div>
				<span></span>
			</div>
		</div>
	</div>
	<div class="js_add_new_form"></div>
</div>
{else}
{_p var='you_have_reached_your_upload_limit'}
{/if}

{if isset($bFixToken)}
	<script type='text/javascript'>		
		$('#user_design_profile').find('input[name^=core]:first').val($('#attachment_js_upload_frame_form').find('input[name^=core]:first').val());
	</script>
{/if}