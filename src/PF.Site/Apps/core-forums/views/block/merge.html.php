<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_error_message"></div>
<form method="post" action="#" onsubmit="$(this).ajaxCall('forum.processMerge'); return false;">
	<input type="hidden" name="thread_id" value="{$aThread.thread_id}" />
	<div class="form-group">
		<label>{_p var='url'}:</label>
		<input type="text" name="url" value="" size="30" class="form-control" />
	</div>
	{if !$bIsGroup}
	<div class="form-group">
		<label>{_p var='destination_forum'}:</label>
		<select name="forum_id" class="form-control">
			{$sForums}
		</select>
	</div>	
	{/if}
	<input type="submit" value="{_p var='merge_threads'}" class="button btn-primary"/>
</form>