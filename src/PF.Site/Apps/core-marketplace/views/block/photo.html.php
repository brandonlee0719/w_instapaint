<?php 
	defined('PHPFOX') or exit('NO DICE!'); 
?>

<div class="market-app manage-photo">
	<div class="block">
		<div class="manage-photo-title">
	        <span class="fw-bold" id="js_listing_total_photo">{$iTotalImage} {if $iTotalImage == 1}{_p var='photo'}{else}{_p var='photos'}{/if}</span>
            <a href="javascript:void(0)" {if $iTotalImage >= $iTotalImageLimit}style="display:none"{/if} id="js_listing_upload_photo" onclick="$Core.marketplace.toggleUploadSection({$iListingId}); return false;" class="fw-bold">
                <i class="ico ico-upload-cloud"></i>&nbsp;{_p var='upload_new_photos'}
            </a>
	    </div>

	    {if count($aImages)}
			<div class="content item-container">
				{foreach from=$aImages name=images item=aImage}
					<article title="{_p var='click_to_set_as_default_image'}" id="js_photo_holder_{$aImage.image_id}" class="px-1 mb-2 js_mp_photo">
						<div class="item-outer">
							<div class="item-media">
								<a href="javascript:void(0)" onclick="$('.is-default').hide(); $(this).siblings('.is-default').show(); $.ajaxCall('marketplace.setDefault', 'id={$aImage.image_id}'); return false;" style="background-image: url('{img server_id=$aImage.server_id path='marketplace.url_image' file=$aImage.image_path suffix='_400_square' max_width='120' max_height='120' class='js_mp_fix_width' return_url=true}');"></a>
								<span class="item-photo-delete" title="{_p var='delete_this_image_for_the_listing'}" onclick="$Core.jsConfirm({l}message:'{_p var='are_you_sure' phpfox_squote=true}'{r}, function(){l} $('#js_photo_holder_{$aImage.image_id}').remove(); $.ajaxCall('marketplace.deleteImage', 'id={$aImage.image_id}&listing_id={$aForms.listing_id}'); $('#js_mp_image_{$aImage.image_id}').remove(); {r}, function(){l}{r}); return false;"><i class="ico ico-close"></i></span>
                                <div class="is-default" {if $aForms.image_path != $aImage.image_path}style="display:none"{/if}><div class="item-default"><i class="ico ico-photo-star-o"></i>{_p var='default_photo'}</div></div>
							</div>
						</div>
					</article>
				{/foreach}
			</div>
	    {else}
	        <div class="help-block">{_p var='no_photos_found'}</div>
	    {/if}
	</div>
</div>