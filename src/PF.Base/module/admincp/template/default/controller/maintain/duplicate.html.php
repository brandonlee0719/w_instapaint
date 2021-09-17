<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="table-responsive">
    <table class="table table-admin">
        <tbody>
        {foreach from=$aLists key=iKey name=list item=aList}
            <tr>
                <td>
                    {$aList.name}
                </td>
                <td class="w100">
                    <a class="btn btn-xs btn-primary" href="{url link='admincp.maintain.duplicate' table=$aList.table}">{_p var='check'}</a>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>