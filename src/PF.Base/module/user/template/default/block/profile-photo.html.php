<?php
defined('PHPFOX') or exit('NO DICE');
?>
<div id="thumbnail" class="tab-pane fade in active">
    <div id="profile_crop_me">
        <div class="image-editor">
            <div class="image-editor-bg"></div>
            <form method="post" action="{url link='user.photo.process'}" dir="ltr" id="update-profile-image-form">
                {module name='core.upload-form' type='user'}
                <div class="cropit-preview"></div>
                <div class="cropit-drag-info"><span>{_p var='drag_to_reposition_photo'}</span></div>
                <div class="cropit-btn-edit">
                    <input type="range" class="cropit-image-zoom-input"/>
                    <button type="button" class="rotate-ccw"><i class="ico ico-rotate-left-alt"></i></button>
                    <button type="button" class="rotate-cw"><i class="ico ico-rotate-right-alt"></i></button>
                </div>
                <input type="hidden" name="image-data" class="hidden-image-data" />
                <div><input type="hidden" name="val[crop-data]" value="" id="crop_it_form_image_data" /></div>
            </form>
        </div>
        <div class="rotate_button">
            <a role="button" class="btn btn-default" onclick="$Core.ProfilePhoto.update(false)">{_p var='change_photo'}</a>
            <input type="button" class="btn btn-primary" value='{_p var="Save"}' onclick="$Core.ProfilePhoto.save()"/>
        </div>
    </div>
</div>

{literal}
<script>
    $Behavior.checkIE = function() {
      if ($Core.getIEVersion() > 0) {
        $('.cropit-btn-edit input').addClass('ie');
      }
      $Behavior.checkIE = function() {};
    }
</script>
{/literal}
