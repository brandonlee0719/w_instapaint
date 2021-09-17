<?php

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="global_attachment_holder_section" id="global_attachment_photo">
	{plugin call='photo.template_block_share_1'}
	<div><input type="hidden" name="val[group_id]" value="{if isset($aFeedCallback.item_id)}{$aFeedCallback.item_id}{else}0{/if}" /></div>
	<div><input type="hidden" name="val[action]" value="upload_photo_via_share" /></div>

    {module name='core.upload-form' type='photo_feed' }

	{plugin call='photo.template_block_share_2'}
</div>
{plugin call='photo.template_block_share_3'}