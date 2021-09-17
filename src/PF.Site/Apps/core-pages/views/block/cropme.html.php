<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="pages_crop_me" class="dont-unbind-children">
    <div class="image-editor">
        <form method="post" action="#" onsubmit="$(this).ajaxCall('pages.processCropme'); return false;" dir="ltr">
        <div class="cropit-preview"></div>
        <div class="rotate_button">
            <a class="rotate-ccw btn"><i class="fa fa-undo" aria-hidden="true"></i></a>
            <a class="rotate-cw btn"><i class="fa fa-repeat" aria-hidden="true"></i></a>
        </div>
        <input type="range" class="cropit-image-zoom-input"/>
        <input type="hidden" name="image-data" class="hidden-image-data" />
        <div><input type="hidden" name="val[crop-data]" value="" id="crop_it_form_image_data" /></div>
        <div><input type="hidden" name="val[page_id]" value="{$aPageCropMe.page_id}" /></div>
        <div class="rotate_button">
            <input type="submit" class="btn btn-primary" value='{_p var="save"}'/>
        </div>
    </div>
</div>
<script>
  Core_Pages.cropmeImgSrc = '{img server_id=$aPageCropMe.image_server_id path='pages.url_image' file=$aPageCropMe.image_path return_url=true }';
</script>