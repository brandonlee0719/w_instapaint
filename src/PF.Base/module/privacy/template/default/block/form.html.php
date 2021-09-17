<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: form.html.php 4854 2012-10-09 05:20:40Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if $bIsListType}
    <div class="{if $sPrivacyFormType == 'mini'}privacy_setting_mini{else}privacy_setting{/if} privacy_setting_div privacy-list-type">
        <input type="hidden" id="{$sPrivacyFormName}" name="val{if !empty($sPrivacyArray)}[{$sPrivacyArray}]{/if}[{$sPrivacyFormName}]" value="{$aSelectedPrivacyControl.value}" />
        <ul>
            {foreach from=$aPrivacyControls name=privacycontrol item=aPrivacyControl}
            <li role="presentation">
                <a {if isset($aPrivacyControl.onclick)} onclick="{$aPrivacyControl.onclick} return false;"{/if} data-toggle="privacy_item" rel="{$aPrivacyControl.value}" {if (isset($aPrivacyControl.is_active)) || (isset($bNoActive) && $bNoActive && $phpfox.iteration.privacycontrol == 1)}class="is_active_image"{/if}>
                    <i class="fa fa-privacy-{$aPrivacyControl.value}"></i> {$aPrivacyControl.phrase}
                </a>
            </li>
            {/foreach}
        </ul>
    </div>
    {if !empty($sPrivacyFormInfo)}
    <p class="help-block">
        {$sPrivacyFormInfo}
    </p>
    {/if}
{else}
    <div class="{if $sPrivacyFormType == 'mini'}privacy_setting_mini{else}privacy_setting{/if} privacy_setting_div">
        <input type="hidden" id="{$sPrivacyFormName}" name="val{if !empty($sPrivacyArray)}[{$sPrivacyArray}]{/if}[{$sPrivacyFormName}]" value="{$aSelectedPrivacyControl.value}" />
        <a data-toggle="dropdown" class="privacy_setting_active{if $sPrivacyFormType == 'mini'} js_hover_title{/if} btn btn-default btn-icon {if !empty($sBtnSize)}{$sBtnSize}{/if}">
            <i class="fa fa-privacy fa-privacy-{$aSelectedPrivacyControl.value}"></i>
            <span class="txt-label">{$aSelectedPrivacyControl.phrase}</span>
            <span class="txt-label js_hover_info">{$aSelectedPrivacyControl.phrase}
            </span>
            <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-checkmark">
            {foreach from=$aPrivacyControls name=privacycontrol item=aPrivacyControl}
            {if isset($aPrivacyControl.onclick)}
            <li class="divider"></li>
            {/if}
            <li role="presentation">
                <a {if isset($aPrivacyControl.onclick)} onclick="{$aPrivacyControl.onclick} return false;"{/if} data-toggle="privacy_item" rel="{$aPrivacyControl.value}" {if (isset($aPrivacyControl.is_active)) || (isset($bNoActive) && $bNoActive && $phpfox.iteration.privacycontrol == 1)}class="is_active_image"{/if}>{$aPrivacyControl.phrase}
                </a>
            </li>
            {/foreach}
        </ul>
    </div>
    {if !empty($sPrivacyFormInfo)}
    <p class="help-block">
        {$sPrivacyFormInfo}
    </p>
    {/if}
{/if}