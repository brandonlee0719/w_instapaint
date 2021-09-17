<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if ($sPrivacy != 'rss.can_subscribe_profile') || !Phpfox::getParam('core.friends_only_community')}
<div class="form-group">
    <label>{$aProfile.phrase}</label>
    <div class="select-option">
        <select class="form-control" name="val[privacy][{$sPrivacy}]">
            {if !isset($aProfile.anyone) && !Phpfox::getParam('core.friends_only_community')}
            <option value="0" {if $aProfile.default==
            '0'} selected="selected"{/if}>{_p var='anyone'}</option>
            {/if}
            {if !isset($aProfile.no_user)}
            {if !Phpfox::getParam('core.friends_only_community')}
            <option value="1" {if $aProfile.default==
            '1'} selected="selected"{/if}>{_p var='community'}</option>
            {/if}
            {if Phpfox::isModule('friend')}
            <option value="2" {if $aProfile.default==
            '2'} selected="selected"{/if}>{_p var='friends_only'}</option>
            {/if}
            {/if}
            <option value="4" {if $aProfile.default==
            '4'} selected="selected"{/if}>{_p var='no_one'}</option>
        </select>
    </div>
</div>
{/if}