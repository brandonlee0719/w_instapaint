<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: country-child.html.php 982 2009-09-16 08:11:36Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !PHPFOX_IS_AJAX || $bForceDiv}
    {if $mCountryChildFilter !== null}
    <div><input type="hidden" name="null" id="js_country_child_is_search" value="1" /></div>
    {/if}
    {if $bAdminSearch}
    <div style=""  class="">
        <label style="margin-top:14px">{_p var='state_province'}</label>
        <div id="js_country_child_id">
    {else}
    <div style="padding: 5px 0px 0px;" id="js_country_child_id" class="form-inline">
    {/if}
{/if}
{if count($aCountryChildren) || $bAdminSearch}
	<select name="{if $mCountryChildFilter === null}val{else}search{/if}[country_child_id]" id="js_country_child_id_value" class="form-control">
		<option value="0">{_p var='state_province'}:</option>
	{foreach from=$aCountryChildren key=iChildId item=sChildValue}
		<option value="{$iChildId}"{if $iCountryChildId == $iChildId} selected="selected"{/if}>{$sChildValue}</option>
	{/foreach}
	</select>
{else}
{if PHPFOX_IS_AJAX && $iCountryChildId > 0}
<div><input type="hidden" name="val[country_child_id]" id="js_country_child_id_value" value="{$iCountryChildId}" /></div>
{/if}
{/if}

{if !PHPFOX_IS_AJAX || $bForceDiv}
        {if $bAdminSearch}
    </div>
    </div>
        {else}
    </div>
        {/if}

{/if}