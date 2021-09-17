<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: counter.html.php 1335 2009-12-17 14:47:04Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bRefresh}
<div class="alert alert-success">
	{_p var='updating_counters_processing_page_current_out_of_page' current=$iCurrentPage page=$iTotalPages}
</div>
{else}
<div class="table-responsive">
    <table class="table table-admin">
        <tbody>
        {foreach from=$aLists key=sModule item=aSubLists}
            {foreach from=$aSubLists item=aList name=counters}
            <tr>
                <td>
                    {$aList.name}
                </td>
                <td>
                    <a class="btn btn-xs btn-success" href="{url link='admincp.maintain.counter' module=$sModule id=$aList.id}">{_p var='update'}</a>
                </td>
            </tr>
            {/foreach}
        {/foreach}
        </tbody>
    </table>
</div>
{/if}