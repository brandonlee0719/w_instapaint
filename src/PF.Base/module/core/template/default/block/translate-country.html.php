<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: translate-country.html.php 1329 2009-12-16 17:01:32Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form class="form" method="post" action="#" onsubmit="tb_remove(); $(this).ajaxCall('core.admincp.translateCountryProcess'); return false;">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
                <p class="form-control-static">{_p var='translating_name' name=$sCountryName}</p>
            </div>
            <div><input type="hidden" name="val[country_iso]" value="{$sCountryIso}" /></div>
            {module name='language.admincp.form' id='text' var_name=$sCountryVarName}
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>