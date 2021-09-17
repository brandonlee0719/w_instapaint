<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if $type != 'module'}
<i class="fa fa-spin fa-circle-o-notch"></i>
{/if}
<div id="admincp_install_ftp_information" {if $type != 'module'} style="display:none;"{/if}>
    <form method="post" action="{url link='admincp.store.ftp' productName=$productName type=$type productId=$productId extra_info=$extra_info}" id="form_store_ftp" enctype="multipart/form-data">
        <div>
            <input type="hidden" name="val[type]" value="{$type}"/>
            <input type="hidden" name="val[productName]" value="{$productName}"/>
            <input type="hidden" name="val[productId]" value="{$productId}"/>
            <input type="hidden" name="val[extra_info]" value="{$extra_info}"/>
            <input type="hidden" name="val[targetDirectory]" value="{$targetDirectory}">
            <input type="hidden" name="val[apps_dir]" value="{$apps_dir}">
        </div>
        <div class="form-group">
            <label for="ftp_upload_method">{_p var='file_upload_method'}</label>
            <select id="ftp_upload_method" name="val[method]"
                    onchange="if (this.value=='file_system') $('.hide_file_system_items').hide(); else $('.hide_file_system_items').show();">
                {foreach from=$listMethod key=sKey value=sMethod}
                    <option value="{$sKey}" {if $sKey==$currentUploadMethod} selected {/if}>
                        {$sMethod}
                    </option>
                {/foreach}
            </select>
            <p class="help-block">{_p var='sftp_require_extension'}</p>
        </div>
        <div class="hide_file_system_items" {if 'file_system'==$currentUploadMethod} style="display: none" {/if}>
            <div class="form-group">
                <label>{_p var='ftp_host_name'}</label>
                <input type="text" class="form-control" placeholder="{_p var='ftp_host_name'}" value="{$currentHostName}" name="val[host_name]"/>
            </div>

            <div class="form-group">
                <label>{_p var='port'}</label>
                <input type="text" class="form-control" placeholder="Port" value="{$currentPort}" name="val[port]"/>
            </div>

            <div class="form-group">
                <label>{_p var='ftp_user_name'}</label>
                <input type="text" class="form-control" placeholder="{_p var='ftp_user_name'}" value="{$currentUsername}" name="val[user_name]"/>
            </div>

            <div class="form-group">
                <label>{_p var='ftp_password'}</label>
                <input type="text" class="form-control" placeholder="{_p var='ftp_password'}" value="" name="val[password]"/>
            </div>
        </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="{_p var='Check permission and finalize'}" name="val[submit]"/>
    </div>
    </form>
</div>

{if $type != 'module'}
{literal}
<script>
	$Ready(function() {
		var f = $('#form_store_ftp');
		$('#ftp_upload_method').val('file_system');
		if($('.error_message').length ==0){
            f.trigger('submit');
        }else{
		    $('.fa.fa-spin').hide();
        }
	});
</script>
{/literal}
{/if}