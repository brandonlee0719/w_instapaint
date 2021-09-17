<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Mail
 * @version 		$Id: edit.html.php 794 2009-07-23 14:00:41Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form class="form" method="post" action="{url link='current'}" id="js_edit_folder_form">
{foreach from=$aFolders item=aFolder}
<div class="p_4" id="js_edit_input_folder_{$aFolder.folder_id}">
	<input class="form-control" type="text" name="val[name][{$aFolder.folder_id}]" value="{$aFolder.name|clean}" size="20" /> <a href="#?call=mail.deleteFolder&amp;id={$aFolder.folder_id}" class="delete_link" title="{_p var='delete'}">{img theme='misc/delete.gif' alt='Delete' class='delete_hover'}</a>
</div>
{/foreach}
<div class="p_4">
	<input type="button" value="{_p var='update'}" class="btn btn-primary" id="js_submit_update_folder" /> <input type="button" value="{_p var='cancel'}" class="btn" id="js_cancel_edit_folder" /> <span id="js_process_form_image"></span>
</div>
</form>