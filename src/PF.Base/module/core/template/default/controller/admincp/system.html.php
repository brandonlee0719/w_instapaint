<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: system.html.php 982 2009-09-16 08:11:36Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='server_overview'}</div>
    </div>
    <div class="table-responsive">
        <table class="table table-admin">
            {foreach from=$aStats key=sKey item=sValue}
            <tr>
                <td class="w200">
                    {$sKey}:
                </td>
                <td>
                    {$sValue}
                </td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>
