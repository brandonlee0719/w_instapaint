<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" action="{url link='admincp.core.country.import'}" enctype="multipart/form-data" class="form">
    <div class="panel panel-default">
        <div class="panel-body">
            {_p var='import_country_package'}
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <label for="import">{_p var='file'}</label>
                <input type="file" name="import" size="40" id="import"/>
            </div>
            <div class="form-group">
                <label for="overwrite">{_p var='overwrite'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="overwrite" value="1" /> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="overwrite" value="0" checked="checked" /> {_p var='no'}</span>
                </div>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='import'}" class="btn btn-primary" />
            </div>
        </div>
    </div>
</form>

<br />
<form method="post" action="{url link='admincp.core.country.import'}" enctype="multipart/form-data" class="form">
    <div class="panel panel-default">
        <div class="panel-body">
            {_p var='import_text_file'}
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <label for="country_iso">{_p var='country'}</label>
                {select_location}
            </div>
            <div class="form-group">
                <label for="file_import">{_p var='file'}</label>
                <input type="file" name="file_import" size="40" id="file_import"/>
                <p class="help-block">
                    {_p var='you_can_upload_a_text_file_with_a_list'}
                </p>
            </div>
            <div class="form-group">
                <label for="utf_encoding">{_p var='enable_utf_encoding'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active">
                        <input type="radio" name="val[utf_encoding]" value="1" id="utf_encoding"/> {_p var='yes'}
                    </span>
                    <span class="js_item_active item_is_not_active">
                        <input type="radio" name="val[utf_encoding]" value="0" checked="checked" id="utf_encoding"/> {_p var='no'}
                    </span>
                </div>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='import'}" class="btn btn-primary" />
            </div>
        </div>
    </div>
</form>