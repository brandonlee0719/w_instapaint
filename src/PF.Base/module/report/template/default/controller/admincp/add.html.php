<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 6474 2013-08-20 06:58:29Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form class="form" method="post" action="{url link='admincp.report.add'}">
    {if $bIsEdit}
        <div>
            <input type="hidden" name="id" value="{$aForms.report_id}" />
            <input type="hidden" value="{$aForms.message}" name="val[name_ori]">
        </div>
    {/if}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='category_details'}</div>
        </div>
        <div class="panel-body">
            <input type="hidden" name="val[product_id]" value="phpfox" />
            <input type="hidden" name="val[module_id]" value="core" />
            {field_language phrase='sPhraseVarName' label='category' field='category' format='val[name][' size=30 maxlength=100}
            <button type="submit" class="btn btn-primary">
                {if $bIsEdit}{_p var='update'}{else}{_p var='add'}{/if}
            </button>
        </div>
    </div>
</form>