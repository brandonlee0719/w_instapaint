<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: note.html.php 2826 2011-08-11 19:41:03Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="t_center">
	<div style="position:absolute; right:0; margin-right:20px; margin-top:2px; display:none;" id="js_save_note">
		{img theme='ajax/small.gif'}
	</div>
	<textarea id="js_admincp_note" name="admincp_note" class="form-control" rows="8" onfocus="$('#js_share_user_status').show();" placeholder="{$sAdminNote}"></textarea>
	<div class="p_4 t_right" id="js_share_user_status" style="display:none;">
		<input type="button" value="{_p var='save'}" class="btn btn-primary" onclick="$('#js_share_user_status').hide(); $('#js_save_note').show(); $('#js_admincp_note').ajaxCall('core.admincp.updateNote'); return false;" />
		<input type="button" name="null" value="{_p var='cancel'}" onclick="$('#js_share_user_status').hide(); return false;" class="btn btn-default" />
	</div>	
</div>