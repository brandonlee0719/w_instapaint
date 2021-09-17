<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: sample.html.php 1297 2009-12-04 23:18:17Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="p_4">
	<div class="p_4">	
		<div class="go_left t_right" style="width:150px;"><b>{_p var='php'}</b>:</div>
		<div><input type="text" name="php" value="_p('{$sCachePhrase}')" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>
	<div class="p_4">	
		<div class="go_left t_right" style="width:150px;"><b>{_p var='php_single_quoted'}</b>:</div>
		<div><input type="text" name="php" value="' . _p('{$sCachePhrase}') . '" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>	
	<div class="p_4">	
		<div class="go_left t_right" style="width:150px;"><b>{_p var='php_double_quoted'}</b>:</div>
		<div><input type="text" name="php" value="&quot; . _p('{$sCachePhrase}') . &quot;" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>		
	<div class="p_4">
		<div class="go_left t_right" style="width:150px;"><b>{_p var='html'}</b>:</div>
		<div><input type="text" name="html" value="{literal}{{/literal}_p var='{$sCachePhrase}'{literal}}{/literal}" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>
	<div class="p_4">
		<div class="go_left t_right" style="width:150px;"><b>{_p var='js'}</b>:</div>
		<div><input type="text" name="html" value="oTranslations['{$sCachePhrase}']" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>	
	<div class="p_4">
		<div class="go_left t_right" style="width:150px;"><b>{_p var='text'}</b>:</div>
		<div><input type="text" name="html" value="{$sCachePhrase}" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>		
</div>