<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="#" onsubmit="$('#js_moving_forum').html($.ajaxProcess('{_p var='moving' phpfox_squote=true}')); $(this).ajaxCall('forum.processMove'); return false;">
	<div><input type="hidden" name="thread_id" value="{$aThread.thread_id}" /></div>
	<div class="form-group">
		<label>
			{_p var='destination_forum'}:
		</label>
		<select name="forum_id" class="form-control">
			{$sForums}
		</select>
	</div>
	<input type="submit" value="{_p var='move_thread'}" class="button btn-primary" /> <span id="js_moving_forum"></span>
</form>