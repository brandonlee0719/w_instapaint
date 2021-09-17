<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 3533 2011-11-21 14:07:21Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="error_message" id="js_mail_folder_add_error" style="display:none;"></div>
<form class="form" method="post" action="#" onsubmit="$Core.processForm('#js_mail_folder_add_submit'); $(this).ajaxCall('mail.addFolder'); return false;">
	<input type="text" name="add_folder" value="" size="40" class="form-control"/>
	<p class="help-block">
		{_p var='enter_the_name_of_your_custom_folder'}
	</p>
	<div class="p_top_4" id="js_mail_folder_add_submit">
		<ul class="table_clear_button">
			<li><input type="submit" value="{_p var='submit'}" class="btn btn-primary" /></li>
			<li class="table_clear_ajax"></li>
		</ul>
		<div class="clear"></div>
	</div>
</form>