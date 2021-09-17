<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if isset($error) && $error}
<div class="error_message">
	{$error}
</div>
{else}
<div class="js_box_actions">
	<div>
		{_p var="Import File"}
		<span>
			<input type="file" name="file" class="ajax_upload" data-url="{url link='admincp.app.add'}">
		</span>
	</div>
</div>
<form method="post" action="{url link='admincp.app.add'}" class="ajax_post" id="create-app">
	<div class="vendor_create">
        <div class="panel panel-default">
            <div class="panel-body">
                <pre id="debug_info" class="hide alert alert-danger"></pre>
                <div class="form-group">
                    <label for="name">{required}{_p var='App ID'}</label>
                    <input required type="text" name="val[name]" placeholder="{_p var='App ID'}" id="create-app-info" class="form-control">
                </div>
            </div>
            <div class="panel-footer">
                <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
            </div>
        </div>
	</div>
</form>
<script type="text/javascript">
		var pingApp = '{url link='admincp.app.ping'}';
		{literal}
		var runPing = function() {
			var scriptUrl = pingApp + '?ping-no-session=1&url=' + encodeURIComponent($('#create-app-info').val()) + '&t=' + (new Date()).getTime();
			$('body').append('<script src="' + scriptUrl + '"><\/script>');
		};
		$Ready(function() {
			$('#create-app').submit(function() {
				$('.table_clear').hide();
				$('#debug_info').show();
				runPing();
			});
		});
		{/literal}
</script>
{/if}