<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond_Benc
 * @package          Phpfox
 * @version          $Id: template-menusub.html.php 2817 2011-08-08 16:59:43Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if isset($aFilterMenus) && is_array($aFilterMenus) && count($aFilterMenus)}
<div class="block" id="js_block_border_core_menusub">
    <div class="title">
        {if isset($sMenuBlockTitle)}
            {$sMenuBlockTitle}
        {else}
            {_p var='menu'}
        {/if}
    </div>
    <div class="content">
        <div class="sub-section-menu header_display">
            {if !empty($aMainSelectedMenu) && !empty($aMainSelectedMenu.var_name)}
            <div class="app-name">{_p var=$aMainSelectedMenu.var_name}{if isset($aMainSelectedMenu.suffix)}{$aMainSelectedMenu.suffix}{/if}</div>
            {/if}
            <ul class="action">
                {foreach from=$aFilterMenus name=filtermenu item=aFilterMenu}
                {if !isset($aFilterMenu.name)}
                <li class="menu_line"></li>
                {else}
                <li class="{if $aFilterMenu.active}active{/if}">
                    <?php
                    if (!empty($this->_aVars['aFilterMenusIcons'][$this->_aVars['aFilterMenu']['name']])):
                        echo sprintf("<span class='%s'></span>", $this->_aVars['aFilterMenusIcons'][$this->_aVars['aFilterMenu']['name']]);
                    endif;
                    ?>
                    <a href="{$aFilterMenu.link}">
                        {$aFilterMenu.name}
                    </a>
                </li>
                {/if}
                {/foreach}
            </ul>
        </div>

        <div class="sub-section-menu-mobile dropdown {if !empty($aSubMenus) || !empty($aCustomMenus)}has-btn-addnew{/if}">
            <span class="btn-toggle" data-toggle="dropdown" aria-expanded="false" role="button">
                <span class="ico ico-angle-down"></span>
            </span>
            <ul class="dropdown-menu">
                {foreach from=$aFilterMenus name=filtermenu item=aFilterMenu}
                {if !isset($aFilterMenu.name)}
                <li class="menu_line"></li>
                {else}
                <li class="{if $aFilterMenu.active}active{/if}">
                    <a href="{$aFilterMenu.link}">
                        {$aFilterMenu.name}
                    </a>
                </li>
                {/if}
                {/foreach}
            </ul>
        </div>
    </div>
</div>
{/if}

{if !empty($aBreadCrumbs) && count($aBreadCrumbs) >= 2}
<div class="container" id="js_block_border_core_breadcrumb">
    <div class="content">
        <div class="row breadcrumbs-holder">
            <div class="clearfix breadcrumbs-top">
                <div class="breadcrumbs-container">
                    <div class="breadcrumbs-list">
                        <ol class="breadcrumb" data-component="breadcrumb">
                            {foreach from=$aBreadCrumbs key=sLink item=sCrumb name=link}
                            <li>
                                <a {if !empty($sLink)}href="{$sLink}" {/if} class="ajax_link">
                                    {$sCrumb|clean}
                                </a>
                            </li>
                            {/foreach}
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/if}
							