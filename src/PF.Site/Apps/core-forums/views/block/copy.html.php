<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="#" onsubmit="$('#js_copying_forum').html($.ajaxProcess('{_p var='copying' phpfox_squote=true}')); $(this).ajaxCall('forum.processCopy'); return false;">
	<input type="hidden" name="thread_id" value="{$aThread.thread_id}" />
	<div class="form-group">
		<label>
			{_p var='new_title'}:
		</label>
		<input type="text" name="title" value="{$aThread.title|clean}" size="30" class="form-control" />
	</div>	

	<div class="form-group">
		<label>{_p var='destination_forum'}:</label>
		<select name="forum_id" class="form-control">
			{$sForums}
		</select>
	</div>

	<input type="submit" value="{_p var='copy_thread'}" class="button btn-primary" />
	<span id="js_copying_forum"></span>
</form>