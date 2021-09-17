<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: oncloud.html.php 6554 2013-08-30 11:27:59Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bNewUpgrade}
<div class="message">
	{_p var='new_version_of_oncloud'} ({$aHostingPackage['latest_version']}) {_p var='is_available'}.
	<a href="{url link=''}" class="hosting_package_upgrade" class="sJsConfirm">{_p var='upgrade_oncloud'}</a>
</div>
{/if}
{if isset($aHostingPackage)}
<div class="hosting_package_name">
	{$aHostingPackage.title} {_p var='package'}
</div>
<div class="info_header">
	{_p var='oncloud_usage'}
</div>
<div class="p_4">
	<div class="info">
		<div class="info_left">
            {_p var='members'}:
		</div>
		<div class="info_right">
			{$sTotalMemberUsage}			
		</div>
	</div>	
	<div class="info">
		<div class="info_left">		
			{_p var='space'}:
		</div>
		<div class="info_right">
			{$sTotalSpaceUsage}
		</div>
	</div>
	<div class="info">
		<div class="info_left">
			{_p var='videos'}:
		</div>
		<div class="info_right">
			{$sTotalVideoUsage}
		</div>
	</div>
	<div class="info">
		<div class="info_left">
			{_p var='latest_version'}:
		</div>
		<div class="info_right">
			{$aHostingPackage['latest_version']}
		</div>
	</div>
</div>
{if $aHostingPackage.can_upgrade}
<a href="http://www.phpfox.com/account/download/{$aHostingPackage.license}/" class="hosting_package_upgrade">{_p var='upgrade_package'}</a>
{/if}
{else}
{_p var='unable_to_load_oncloud_info'}.
{/if}