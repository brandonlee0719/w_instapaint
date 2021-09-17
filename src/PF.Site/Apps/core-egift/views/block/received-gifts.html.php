<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="egift-recent-container">
{foreach from=$aGifts key=sName name=row item=aGift}
<div class="egift-recent-item">
	<div class="item-outer">
		<div class="item-media">
			<span style="background-image: url({img id='js_egift_item_image_'.$aGift.egift_id server_id=$aGift.server_id path='egift.url_egift' file=$aGift.file_path suffix='_120' max_width=120 max_height=120 return_url=true});"></span>
		</div>
		<div class="item-inner">
			<div class="item-title">{$aGift.title}</div>
		</div>
	</div>
</div>
{/foreach}
</div>