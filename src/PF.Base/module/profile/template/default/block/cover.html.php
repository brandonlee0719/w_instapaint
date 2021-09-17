<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="js_cover_photo_iframe_loader_error"></div>
<div id="js_cover_photo_iframe_loader_upload" style="display:none;">{img theme='ajax/add.gif' class='v_middle'} {_p var='uploading_image'}</div>
<form class="form" onsubmit="$('#js_cover_photo_iframe_loader_error').hide(); $('#js_cover_photo_iframe_loader_upload').show(); $('#js_activity_feed_form').hide();" enctype="multipart/form-data" action="{url link='photo.frame'}" method="post" target="js_cover_photo_iframe_loader">
	<div><input type="hidden" name="val[action]" value="upload_photo_via_share" /></div>
	<div><input type="hidden" name="val[is_cover_photo]" value="1" /></div>
	{if isset($iPageId) && !empty($iPageId)}
		<div>
			<input type="hidden" name="val[page_id]" value="{$iPageId}" />
		</div>
	{/if}
	{if isset($iGroupId) && !empty($iGroupId)}
		<div>
			<input type="hidden" name="val[groups_id]" value="{$iGroupId}" />
		</div>
	{/if}
	<div class="form-group">
        <div><input type="file" accept="image/*" name="image[]" id="global_attachment_photo_file_input" value="" onchange="$(this).parents('form:first').submit();" class="form-control"/></div>
	</div>
	<div class="table_clear" style="display:none;">
		<div><input type="submit" value="{_p var='upload'}" class="btn btn-primary"/></div>
	</div>	
	<iframe id="js_cover_photo_iframe_loader" name="js_cover_photo_iframe_loader" height="200" width="500" frameborder="1" style="display:none;"></iframe>
</form>