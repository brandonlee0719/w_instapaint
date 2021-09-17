<?php

defined('PHPFOX') or exit('NO DICE!');

?>

<div id="js_marketplace_form_holder" class="uploader-photo-fix-height">
    {if $iTotalImage < $iTotalImageLimit}
        {module name='core.upload-form' type='marketplace' params=$aForms.params}
        <div class="market-app cancel-upload">
            <a href="javascript:void(0)" onclick="$Core.marketplace.toggleUploadSection({$iListingId}); return false;"><i class="ico ico-arrow-left"></i>&nbsp;{_p var='back_to_manage'}</a>
            <a href="{permalink module='marketplace' id=$iListingId title=$aForms.title}" id="js_listing_done_upload" style="display: none;" class="text-uppercase"><i class="ico ico-check"></i>&nbsp;{_p var='finish_upload'}</a>
        </div>
    {else}
        <p>{_p var='you_cannot_add_more_image_to_your_listing'}</p>
    {/if}
</div>
