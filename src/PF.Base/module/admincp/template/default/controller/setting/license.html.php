<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if $bCanWrite}
    <form method="post" class="form" action="{url link='current'}" id="js_form">
        <div id="client_details" class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">{_p var='Enter your license ID & Key:'}</div>
            </div>
            <div class="panel-body"><div><input type="hidden" id="license_trial" name="val[is_trial]" value="0"></div>
                <div class="form-group">
                    <label for="license_id">{_p var='License ID'}</label>
                    <input required class="form-control"  autocomplete="off" type="text" name="val[license_id]" id="license_id" value="{$aVals.license_id}" size="30" placeholder="{_p var='License ID'}" />
                </div>
                <div class="form-group">
                    <label for="license_key">{_p var='License Key'}</label>
                    <input required class="form-control" autocomplete="off" type="text" name="val[license_key]" id="license_key" value="{$aVals.license_key}" size="30" placeholder="{_p var='License Key'}" />
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit" role="button">{_p var='save_changes'}</button>
                    <a class="btn btn-info" role="button" href="{url link='admincp'}">{_p var='cancel'}</a>
                </div>
            </div>
        </div>
    </form>
{else}
    <div class="error error_message">
        {_p var="Do not permission to edit file 'PF.Base/settings/license.sett.php'. Please change its permission or use ftp to edit it"}
    </div>
{/if}
