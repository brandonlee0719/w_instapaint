<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: ip.html.php 1025 2009-09-21 09:24:56Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aResults) && is_array($aResults)}
{foreach from=$aResults item=aResult}
<div class="table_header">
	{$aResult.table}
</div>
{if isset($aResult.th)}
<div class="table-responsive">
    <table class="table table-admin">
        <thead>
            <tr>
            {foreach from=$aResult.th item=sTh}
                <th>{$sTh}</th>
            {/foreach}
            </tr>
        </thead>
        <tbody>
            {foreach from=$aResult.results key=iKey item=aValues}
            <tr{if is_int($iKey/2)} class="tr"{/if}>
            {foreach from=$aValues item=sValue}
                <td>{$sValue}</td>
            {/foreach}
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
{else}
{if isset($aResult.results)}
{foreach from=$aResult.results key=sKey item=sValue}
<div class="table form-group">
	<div class="table_left">
		{$sKey}:
	</div>
	<div class="table_right">
		{$sValue}
	</div>
	<div class="clear"></div>
</div>
{/foreach}
{/if}
{/if}
{/foreach}
{else}
<form method="post" action="{url link='admincp.core.ip'}" class="">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='search'}</div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="search">{_p var='ip_address'}</label>
                <input type="text" name="search" value="" class="form-control" size="40" id="search"/>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='search'}" class="btn btn-primary" />
            </div>
        </div>
    </div>
</form>
{/if}