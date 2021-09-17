<form action="{url link='admincp.im.manage-sound'}" method="post" enctype="multipart/form-data">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='manage_notification_sound'}</div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{_p var='select_notification_sound_type'}</label>
                <div class="radio">
                    <label><input type="radio" name="noti-sound-type" value="default" {if !isset($option) || $option == 'default'}checked{/if}>{_p var='default'}</label><br/>
                    <label><input type="radio" name="noti-sound-type" value="custom" {if isset($option) && $option == 'custom'}checked{/if}>{_p var='custom'}</label>
                </div>
            </div>
            <div class="form-group">
                <label for="noti-sound-file">{_p var='custom_notification_sound'}</label>
                <input type="file" name="noti-sound-file" id="noti-sound-file" accept="audio/*">
                <p class="help-block">{_p var='you_can_upload_a_mp3_wav_or_ogg_file'}</p>
                <p>{_p var='current_custom_notification_sound_path'}: <b>{if !isset($custom_file) || $custom_file == null}{_p var='none'}{else}{$custom_file}{/if}</b></p>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">{_p var='submit'}</button>
            </div>
        </div>
    </div>
</form>