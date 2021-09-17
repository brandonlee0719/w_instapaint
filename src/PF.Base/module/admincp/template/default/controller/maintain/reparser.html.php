<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: reparser.html.php 1194 2009-10-18 12:43:38Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!');

?>
{if isset($bInProcess)}
<div class="alert alert-info" role="alert">
	{_p var='parsing_page_current_total_please_hold' current=$iCurrentPage total=$iTotalPages}
</div>
{else}
{if count($aReparserLists)}
<div class="table-responsive">
    <table class="table table-admin">
        <tbody>
        {foreach from=$aReparserLists key=sModule name=list item=aReparserList}
            <tr">
                <td>
                    {$aReparserList.name}
                </td>
                <td class="w100">
                    <a class="btn btn-xs btn-primary" href="{url link='admincp.maintain.reparser' module=$sModule}">{_p var='run'} ({_p var='records'}: {$aReparserList.total_record})</a>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
{else}
<div class="alert alert-success" role="alert">
	{_p var='there_is_no_data_to_parse'}
</div>
{/if}
{/if}