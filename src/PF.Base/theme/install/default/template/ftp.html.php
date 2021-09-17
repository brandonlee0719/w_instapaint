<?php
defined('PHPFOX') or exit('NO DICE!');
/**
 * @author Neil J.<neil@phpfox.com>
 */
?>
<form method="post" action="#ftp" id="js_form" class="form" enctype="multipart/form-data">
    <h1>FTP Information</h1>
    <input type="submit" class="hide" value="Check permission and continue" name="val[submit]"/>
    <div id="errors" class="hide"></div>
    <div class="form-group">
        <label class="control-label" for="ftp_upload_method">File Upload Method:</label>
        {foreach from=$listMethod key=sKey value=sMethod}
        <div class="radio">
            <label><input onchange="installer.fileSystemChanged(this.form,this.value)" type="radio" value="{$sKey}" name="val[method]" {if $sKey=='file_system'}checked {/if}>{$sMethod}</label>
        </div>
        {/foreach}
    </div>
    <div class="form-group method method_ftp method_sftp_ssh method_key hide">
        <label class="control-label">FTP host name</label>
        <input autofocus type="text" class="form-control" placeholder="FTP host name" value="" name="val[host_name]"/>
    </div>

    <div class="form-group method method_ftp method_sftp_ssh method_key hide">
        <label class="control-label">Port</label>
        <input type="text" class="form-control" placeholder="Port" value="" name="val[port]"/>
    </div>

    <div class="form-group method method_ftp method_sftp_ssh method_key hide">
        <label class="control-label">FTP User name</label>
        <input type="text" class="form-control" placeholder="FTP User name" value="" name="val[user_name]"/>
    </div>

    <div class="form-group method method_ftp method_sftp_ssh hide">
        <label class="control-label">FTP password</label>
        <input type="text" class="form-control" placeholder="FTP password" value="" name="val[password]"/>
    </div>

    <div class="form-group method method_key hide">
        <label class="control-label">Private Key file</label>
        <input type="hidden" name="val[key]" id="fileprivate_content"/>
        <input type="file" name="fileprivate" id="fileprivate" onchange="installer.readPrivateFile();"/>
    </div>

    <div class="form-group method method_key hide">
        <label class="control-label">Passphrase</label>
        <input type="text" class="form-control" placeholder="Passphrase" value="" name="val[passphrase]"/>
    </div>
    <div class="help-block">
        If you encounter any problem, please follow our instruction in <a href="https://docs.phpfox.com/display/FOX4MAN/Installing+phpFox" target="_blank">this help topic</a> then try again.
    </div>
</form>