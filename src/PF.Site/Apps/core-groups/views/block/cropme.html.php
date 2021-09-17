<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="pages_crop_me" class="dont-unbind-children">
    <div class="image-editor">
        <form method="post" action="#" onsubmit="$(this).ajaxCall('groups.processCropme'); return false;" dir="ltr">
            <div class="cropit-preview"></div>
            <div class="rotate_button">
                <a class="rotate-ccw btn"><i class="fa fa-undo" aria-hidden="true"></i></a>
                <a class="rotate-cw btn"><i class="fa fa-repeat" aria-hidden="true"></i></a>
            </div>
            <input type="range" class="cropit-image-zoom-input"/>
            <input type="hidden" name="image-data" class="hidden-image-data" />
            <div><input type="hidden" name="val[crop-data]" value="" id="crop_it_form_image_data" /></div>
            <div><input type="hidden" name="val[page_id]" value="{$aGroupCropMe.page_id}" /></div>
            <div class="rotate_button">
                <input type="submit" class="btn btn-primary" value='{_p var="Save"}'/>
            </div>
        </form>
    </div>
</div>
<script>
    {literal}
    $Behavior.crop_groups_image_photo = function() {
        $('.image-editor').cropit({
            imageState: {
                src: '{/literal}{img server_id=$aGroupCropMe.image_server_id path='pages.url_image' file=$aGroupCropMe.image_path return_url=true }{literal}'
            },
            smallImage: 'allow',
            maxZoom: 2
        });
        
        $('.rotate-cw').click(function() {
            $('.image-editor').cropit('rotateCW');
        });
        $('.rotate-ccw').click(function() {
            $('.image-editor').cropit('rotateCCW');
        });
        
        $('.export').click(function() {
            var imageData = $('.image-editor').cropit('export');
            window.open(imageData);
        });
    };
    {/literal}
</script>