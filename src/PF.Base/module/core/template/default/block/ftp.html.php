<?php 
defined('PHPFOX') or exit('NO DICE!');
//Can't find this file is used anywhere
?>
<div id="js_ftp_path" style="display:none;">
	<div class="p_4">
		{_p var='ftp_path'}: <input type="text" name="null" value="" id="js_ftp_actual_path" onclick="this.select();" />
		<div class="extra_info" id="js_empty_ftp_path" style="display:none;">
			{_p var='your_ftp_path_is_empty_and_does_not_need_to_have_any_value'}
		</div>
	</div>
</div>
<div id="js_ftp_error" class="error_message" style="display:none;"></div>
<div id="js_ftp_form">
	<form method="post"  class="form" action="#" onsubmit="$('#js_ftp_check_process').html($.ajaxProcess('Checking')); $(this).ajaxCall('core.ftpPathSearch'); return false;">

        <div class="panel panel-default">
            <div class="panel-body">
                {_p var='ftp_details'}
            </div>
            <div class="panel-footer">
                <div class="form-group">
                    <label for="host">{_p var='ftp_host'}</label>
                    <input type="text" name="val[host]" value="" size="30" id="host"/>
                </div>

                <div class="form-group">
                    <label for="user_name">{_p var='ftp_username'}</label>
                    <input type="text" name="val[user_name]" value="" size="30" />
                </div>

                <div class="form-group">
                    <label for="password">{_p var='ftp_password'}</label>
                    <input type="password" name="val[password]" value="" size="30" autocomplete="off" />
                </div>
            </div>
        </div>
		<div class="table_clear">
			<span id="js_ftp_check_process"></span> <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
		</div>
	</form>
</div>