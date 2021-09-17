<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr{*$sLocaleDirection*}" lang="{$sLocaleCode}">
    {if !isset($bShowClearCache)}
    {assign var='bShowClearCache' value=false}
    {/if}
	<head>
    <title>{title}</title>
	{header}
	</head>
	<body class="admincp-fixed-menu">
		<div id="admincp_base"></div>
		<div id="global_ajax_message"></div>
		<div id="header">
			<a href="#" class="header_logo">{_p var='module_admincp'}</a>
            <div class="breadcrumbs">
                {if isset($aAdmincpBreadCrumb) && !empty($aAdmincpBreadCrumb)}
                {foreach from=$aAdmincpBreadCrumb key=sUrl item=sPhrase}
                <a class="" href="{$sUrl}">{$sPhrase}</a>
                {/foreach}
                {else if($sSectionTitle)}
                <a class="" href="">{$sSectionTitle}</a>
                {/if}

            </div>
            <div class="admincp_header_form admincp_search_settings">
                <span class="remove"><i class="fa fa-remove"></i></span>
                <input type="text" name="setting" placeholder="Search settings..." autocomplete="off">
                <div class="admincp_search_settings_results hide">
                </div>
            </div>
		</div>
		<aside class="">
            <div class="admincp_user">
                <div class="admincp_user_image">
                    {img user=$aUserDetails suffix='_50_square'}
                </div>
                <div class="admincp_user_content">
                    {$aUserDetails|user}<br/>
                    <a class="label label-danger" href="{url link=''}">{_p var='view_site'}</a><br/>
                </div>
            </div>
            <ul class="">
                {php}
                    $this->_aVars['aAdminMenus'] =  Phpfox::getService('admincp.sidebar')->prepare()->get();
                {/php}
                {foreach from=$aAdminMenus key=sPhrase item=sLink}
                    {if is_array($sLink)}
                    {assign var='menuId' value="id_menu_item_"$sPhrase}
                    <li id="{$menuId}" {if $sLastOpenMenuId == $menuId}class="open"{/if}>
                <a href="{$sLink.link}" data-tags="{if isset($sLink.tags)}{$sLink.tags}{/if}"
                           {if isset($sLink.items) and !empty($sLink.items)}class="item-header {if isset($sLink.is_active)}is_active{/if}" data-cmd="admincp.open_sub_menu"{else}{if isset($sLink.is_active)}class="is_active"{/if}{/if}>
                        {if isset($sLink.items) and !empty($sLink.items)}{else}{if !empty($sLink.icon)}<i class="{$sLink.icon}"></i>{/if}{/if}
                        {$sLink.label}
                            {if isset($sLink.items) and !empty($sLink.items)}
                            <i class="fa fa-caret"></i>
                            {/if}
                        {if isset($sLink.badge) && $sLink.badge > 0}
                        <span class="badge">{$sLink.badge}</span>
                        {/if}
                        </a>

                    {if isset($sLink.items) and !empty($sLink.items)}
                        <ul>
                            {foreach from=$sLink.items item=sLink2}
                            <li><a data-tags="{if isset($sLink2.tags)}{$sLink2.tags}{/if}" href="{$sLink2.link}" class="{if isset($sLink2.class)}{$sLink2.class}{/if}{if isset($sLink2.is_active)}is_active{/if}">{if !empty($sLink2.icon)}<i class="{$sLink2.icon}"></i>{/if}{$sLink2.label}</a></li>
                            {/foreach}
                        </ul>
                    {/if}
                </li>
                {/if}
                {/foreach}
            </ul>
            <br/>
            <br/>
            <br/>
            <br/>
		</aside>
        <!-- end action menu-->
        <div class="main_holder">
            {if !empty($aActionMenu) or !empty($aSectionAppMenus)}
            <div class="toolbar-top">
                {if isset($aSectionAppMenus) && count($aSectionAppMenus) <= 6}
                <div class="btn-group">
                    {foreach from=$aSectionAppMenus key=sPhrase item=aMenu}
                    <a {if isset($aMenu.cmd)}data-cmd="{$aMenu.cmd}"{/if}  href="{if (substr($aMenu.url, 0, 1) == '#')}{$aMenu.url}{else}{url link=$aMenu.url}{/if}"
                       class="btn btn-default {if isset($aMenu.is_active) && $aMenu.is_active}active{/if}">{$sPhrase}</a>
                    {/foreach}
                </div>
                {/if}
                {if isset($aSectionAppMenus) && count($aSectionAppMenus) > 6}
                    <div class="btn-group">
                    {foreach from=$aSectionAppMenus key=sPhrase item=aMenu name=fkey}
                    {if $phpfox.iteration.fkey < 6}
                    <a {if isset($aMenu.cmd)}data-cmd="{$aMenu.cmd}"{/if}  href="{if (substr($aMenu.url, 0, 1) == '#')}{$aMenu.url}{else}{url link=$aMenu.url}{/if}"
                    class="btn btn-default {if isset($aMenu.is_active) && $aMenu.is_active}active{/if}">{$sPhrase}</a>
                    {/if}
                    {if $phpfox.iteration.fkey == 6}
                        <a class="btn btn-default dropdown-toggle" id="dropdownMenu1" href="" data-toggle="dropdown" aria-expanded="true" aria-haspopup="true">
                            {_p var="more"}
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                    {/if}
                    {if $phpfox.iteration.fkey >= 6}
                        <li role="menuitem">
                        <a {if isset($aMenu.cmd)}data-cmd="{$aMenu.cmd}"{/if}  href="{if (substr($aMenu.url, 0, 1) == '#')}{$aMenu.url}{else}{url link=$aMenu.url}{/if}"
                        class="{if isset($aMenu.is_active) && $aMenu.is_active}active{/if}">{$sPhrase}</a>
                        </li>
                    {/if}
                    {/foreach}
                    </ul>
                    </div>
                {/if}
                {if isset($aActionMenu)}
                <div class="btn-group">
                    {foreach from=$aActionMenu key=sPhrase item=sUrl}
                    {if is_array($sUrl)}
                    <a {if isset($sUrl.cmd)}data-cmd="{$sUrl.cmd}"{/if}  href="{$sUrl.url}" class="btn btn-default {if isset($sUrl.class)}{$sUrl.class}{/if}" {if isset($sUrl.custom)} {$sUrl.custom}{/if}>{$sPhrase}</a>
                    {else}
                    <a class="" href="{$sUrl}">{$sPhrase}</a>
                    {/if}
                    {/foreach}
                </div>
                {/if}
            </div>
            {/if}

            {if (isset($has_upgrade) && $has_upgrade)}
            <br/>
            <div class="alert alert-danger mb-base">
                {_p var="There is an update available for this product."} <a class="btn btn-link" href="{$store.install_url}">{_p var="Update Now"}</a>
            </div>
            {/if}
            <div id="js_content_container">
                <div id="main">
                    {if isset($aSectionAppMenus)}
                    <div class="apps_content">
                        {/if}

                        {error}
                        <div class="_block_content">
                            {content}
                        </div>

                        {if isset($aSectionAppMenus)}
                    </div>
                    {/if}
                </div>
            </div>
            <div id="copyright">
                {param var='core.site_copyright'} {product_branding}
            </div>
        </div>
		{plugin call='theme_template_body__end'}	
        {loadjs}
	</body>
</html>