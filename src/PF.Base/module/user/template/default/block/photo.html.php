<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" class="form" action="{url link='user.photo'}" enctype="multipart/form-data" target="js_upload_photo_frame">
    <input type="hidden" name="val[is_iframe]" value="1" />
    <input type="hidden" name="val[user_id]" value="{$iUserId}" />
    <div class="panel panel-default">
        <div class="panel-body">
            {_p var='select_an_image'}
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <label for="file_image">{_p var='file'}</label>
                <input type="file" name="image" id="file_image" accept="image/*" />
                <p class="help-block">
                    {_p var='you_can_upload_a_jpg_gif_or_png_file'}
                </p>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='upload_picture'}" class="btn btn-primary" name="val[uploaded]" />
            </div>
        </div>
    </div>
</form>

<iframe class="hide" frameborder="1" name="js_upload_photo_frame" id="js_upload_photo_frame" style="width:100%; height:200px;"></iframe>