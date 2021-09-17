<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !empty($aPackages.packages)}
<div class="membership-comparison-container">
    <div class="item-title-block">{_p var='plans_comparision'}</div>
    <div id="subscribe_compare_plan">
    <div id="div_compare_wrapper" class="table-responsive">
		<table class="table">
            <thead>
            <tr>
                <th class="item-feature"></th>
            {foreach from=$aPackages.packages item=aPackage}
                <th class="item-compare" style="background-color: {$aPackage.background_color};">{_p var=$aPackage.title}</th>
            {/foreach}
            </tr>
            </thead>
            <tbody>
            {foreach from=$aPackages.features key=sFeature item=aFeatures}
            <tr>
                <td class="item-feature"><span>{$sFeature}</span></td>
                {foreach from=$aFeatures item=aFeature}
                <td class="item-compare">
                    {if $aFeature.feature_value=='img_accept.png'}
                    <span class="ico ico-check"></span>
                    {elseif $aFeature.feature_value=='img_cross.png'}
                    {else}
                    <span>{$aFeature.feature_value}</span>
                    {/if}
                </td>
                {/foreach}
            </tr>
            {/foreach}
            </tbody>
        </table>
	</div>
</div>
</div>
{/if}