<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="core-egift-wrapper">
    <div id="js_core_egift_preview" class="item-main-egift"></div>
    <div class="item-icon-egift">
        <input type="hidden" name="val[egift_id]" id="js_core_egift_id" value="">
        <a onclick="tb_show('', $.ajaxBox('egift.showEgifts', 'is_user_birthday={$aUser.is_user_birthday}')); return false;"><i class="ico ico-gift-o" aria-hidden="true"></i></a>
    </div>
</div>

{literal}
<script>
    $Behavior.core_egifts_onloaddisplay = function () {
        $('#activity_feed_submit').click(function () {
            if ($('textarea[name="val[user_status]"]').length > 0 && $('textarea[name="val[user_status]"]').val() != '') {
                $('#js_core_egift_preview').html('');
            }
        });
    }
</script>
{/literal}
