<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if $uninstall}
<div class="panel">
    <div class="panel-body">
	<div class="error_message">
		{_p var='to_continue_with_uninstalling__please_enter_your_admin_login_details'}.
	</div>
	<form method="post" action="{url link='current'}" class="form ajax_post">
		<div class="form-group">
			<label for="email">{_p var='email'}</label>
            <input type="text" id="email" name="val[email]" class="form-control">
		</div>

		<div class="form-group">
			<label for="password">{_p var='password'}</label>
            <input type="password" id="password" name="val[password]" autocomplete="off" class="form-control">
		</div>
        {*if $App.allow_remove_database}
        <div class="form-group">
			<label for="rmdb">
                <input id="rmdb" type="checkbox" name="val[rmdb]" value="1" checked="checked"/> &nbsp;{_p var='remove_database_of_this_app'}
			</label>
		</div>
        {/if*}
		<div style="hide">
            <div class="error_message">
                {_p var='please_re_type_your_ftp_account'}
            </div>
            <div class="session_ftp_account">
                <div class="form-group">
                    <label for="method">{_p var='file_upload_method'}</label>
                    <select id="method" name="val[method]"
                            onchange="if (this.value=='file_system') $('.hide_file_system_items').hide(); else $('.hide_file_system_items').show();">
                        {foreach from=$listMethod key=sKey value=sMethod}
                        <option value="{$sKey}" {if $sKey==$currentUploadMethod} selected {/if}>
                        {$sMethod}
                        </option>
                        {/foreach}
                    </select>
                </div>

                <div class="hide_file_system_items {if 'file_system'==$currentUploadMethod}hide{/if}">
                    <div class="form-group">
                        <label for="host_name">{_p var='ftp_host_name'}</label>
                        <input type="text" id="host_name" class="form-control" placeholder="{_p var='ftp_host_name'}"  value="{$currentHostName}" name="val[host_name]"/>
                    </div>

                    <div class="form-group">
                        <label for="port">{_p var="Port"}</label>
                        <input type="text" class="form-control" id="port" placeholder="{_p var='Port'}" value="{$currentPort}" name="val[port]"/>
                    </div>

                    <div class="form-group">
                        <label for="user_name">{_p var='ftp_user_name'}</label>
                        <input type="text" id="user_name" class="form-control" placeholder="{_p var='ftp_user_name'}"  value="{$currentUsername}" name="val[user_name]"/>
                    </div>

                    <div class="form-group">
                        <label for="ftp_password">{_p var='ftp_password'}</label>
                        <input type="text" id="ftp_password" class="form-control" placeholder="{_p var='ftp_password'}" value="{$currentPassword}" name="val[ftp_password]"/>
                    </div>
                </div>
            </div>
        </div>
		<div class="form-group">
            <button type="submit" class="btn btn-primary" name="_submit">{_p var='Submit'}</button>
		</div>
	</form>
    </div>
</div>
{else}
    {if !PHPFOX_IS_AJAX_PAGE}
    <div id="app-custom-holder" style="min-height:400px;"></div>
    <div id="app-content-holder">
    {/if}
		{if $customContent}
		<div id="custom-app-content"><i class="fa fa-circle-o-notch fa-spin"></i></div>
		<script>
			var customContent = '{$customContent}', contentIsLoaded = false, extraParams = {$extraParams}, appUrl = '{$appUrl}';
		{literal}
			$Ready(function() {
                contentIsLoaded =  _admincp_load_content(customContent, contentIsLoaded, extraParams, appUrl);
			});
		{/literal}
		</script>
		{/if}
    {if !PHPFOX_IS_AJAX_PAGE}
	</div>
    <div id="app-details">
        {if (!$ActiveApp.is_core)}
        <ul>
            <li><a {if $App.is_module}class="sJsConfirm" data-message="{_p var='are_you_sure' phpfox_squote=true}"{/if} href="{$uninstallUrl}">{_p var='uninstall'}</a></li>
            {if $export_path && defined('PHPFOX_IS_TECHIE') && PHPFOX_IS_TECHIE}
            <li><a href="{$export_path}">{_p var="Export"}</a></li>
            {/if}
        </ul>
        {/if}
        <div class="app-copyright">
            {if $ActiveApp.vendor}
            ©{$ActiveApp.vendor}
            {/if}
            {if $ActiveApp.credits}
            <div class="app-credits">
                <div>{_p var="Credits"}</div>
                {foreach from=$ActiveApp.credits item=url key=name}
                <ul>
                    <li><a href="{$url}">{$name|clean}</a></li>
                </ul>
                {/foreach}
            </div>
            {/if}
        </div>
    </div>
    {/if}
{/if}