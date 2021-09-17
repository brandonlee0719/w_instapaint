<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: breadcrumb.html.php 5844 2013-05-09 08:00:59Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($aBreadCrumbs) && count($aBreadCrumbs) > 0}
<div class="row breadcrumbs-holder">
	{if isset($aBreadCrumbs) && count($aBreadCrumbs) > 0}
	<div class="clearfix breadcrumbs-top">
		<div class="pull-left">
			<div class="breadcrumbs-list">
				{if isset($aBreadCrumbs)}
                <ol class="breadcrumb" data-component="breadcrumb">
                    {foreach from=$aBreadCrumbs key=sLink item=sCrumb name=link}
                    <li>
                        <a {if !empty($sLink)}href="{$sLink}" {/if} class="ajax_link">
                            {$sCrumb|clean}
                        </a>
                    </li>
                    {/foreach}
                    {if !$bIsDetailPage && !defined('PHPFOX_APP_DETAIL_PAGE') && !empty($aBreadCrumbTitle)}
                    <li><a href="{ $aBreadCrumbTitle[1] }" class="ajax_link">{ $aBreadCrumbTitle[0] }</a></li>
                    {/if}
                </ol>
				{/if}
			</div>
		</div>
		<div class="pull-right breadcrumbs_right_section">
			{breadcrumb_menu}
		</div>
	</div>
	{/if}
	{if ($bIsDetailPage || defined('PHPFOX_APP_DETAIL_PAGE')) && !empty($aBreadCrumbTitle)}
        <h1 class="breadcrumbs-bottom">
            <a href="{ $aBreadCrumbTitle[1] }" class="ajax_link">{ $aBreadCrumbTitle[0] }</a>
            {if !empty($aPageExtraLink)}
            <div class="view_item_link">
                <a href="{$aPageExtraLink.link}" class="page_section_menu_link" title="{$aPageExtraLink.phrase}">
                    <span>{$aPageExtraLink.phrase}</span>
                </a>
            </div>
            {/if}
        </h1>
	{/if}
</div>
{/if}