<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{plugin call='ad.template_block_display__start'}

{if (!PHPFOX_IS_AJAX && !defined('PHPFOX_IS_AD_IFRAME')) || $bBlockIdForAds}
<div class="js_ad_space_parent">
    <div id="js_ad_space_{$iBlockId}" class="t_center ad_space ad_multi_container">
{/if}

{foreach from=$aBlockAds item=aAd name=iAds}
<div {if Phpfox::getParam('ad.multi_ad')}class="multi_ad_holder"{/if}>
    {if $aAd.type_id == 1}
        <a href="{url link='ad' id=$aAd.ad_id}" target="_blank" class="no_ajax_link">
            {img file=$aAd.image_path path='ad.url_image' server_id=$aAd.server_id suffix='_500'}
        </a>
    {else}
        {if (!defined('PHPFOX_IS_AD_IFRAME') && ((defined('PHPFOX_IS_AJAX_PAGE') && PHPFOX_IS_AJAX_PAGE) || $bBlockIdForAds || Phpfox::getParam('ad.ad_ajax_refresh'))) && !Phpfox::getParam('ad.multi_ad')}
        <iframe src="{url link='ad.iframe' id={$aAd.location resize=true}adId_{$aAd.ad_id}" allowtransparency="true" id="js_ad_space_{$aAd.location}_frame_{$aAd.ad_id}" frameborder="0" style="width:100%; "></iframe>
        {else}
            {if Phpfox::getParam('ad.multi_ad') != true}
                {$aAd.html_code}
            {else}
                <div class="ad_unit_multi_ad" onclick="window.open('{url link='ad' id=$aAd.ad_id}')">
                    
                    <div class="ad_unit_multi_ad_image js_ad_image">
                        {if ($aAd.image_path)}
                        <span class="ad-media" style="background-image: url({img file=$aAd.image_path path='ad.url_image' server_id=$aAd.server_id return_url=true suffix='_500'})"></span>
                        {else}
                            {img file=$aAd.image_path path='ad.url_image' server_id=$aAd.server_id suffix='_500'}
                        {/if}
                    </div>
                    <div class="ad_unit_multi_ad_content">
                        
                        <div class="ad_unit_multi_ad_title">
                            {$aAd.title}
                        </div>
                        <div class="ad_unit_multi_ad_url" title="{$aAd.trimmed_url}" data-toggle="tooltip">
                            {$aAd.trimmed_url}
                        </div>
                        <div class="ad_unit_multi_ad_text">
                            {$aAd.body}
                        </div>
                    </div>
                </div>
            {/if}
        {/if}
    {/if}
</div>

{if Phpfox::getParam('ad.ad_ajax_refresh') && defined('PHPFOX_IS_AD_IFRAME')}
<script type="text/javascript">
{if $aAd.type_id == 2}
        window.parent.$(function()
        {l}
            if (window.parent.$('#js_ad_space_{$aAd.location}_frame').length > 0)
            {left_curly}
                setTimeout("window.parent.$('#js_ad_space_{$aAd.location}_frame').attr('src', '{url link='ad.iframe' id={$aAd.location}');", ({param var='ad.ad_ajax_refresh_time'} * 60000));
            {right_curly}
            else
            {left_curly}
                setTimeout("window.parent.$('#js_ad_space_{$aAd.location}').html('<iframe class=\"js_ad_space_iframe\" allowtransparency=\"true\" id=\"js_ad_space_{$aAd.location}_frame_{$aAd.ad_id}\" frameborder=\"0\" style=\"width:' + window.parent.$('#js_ad_space_{$aAd.location}').width() + 'px; height:' + window.parent.$('#js_ad_space_{$aAd.location}').height() + 'px;\"></iframe>'); window.parent.$('#js_ad_space_{$aAd.location}_frame').attr('src', '{url link='ad.iframe' id={$aAd.location}');", ({param var='ad.ad_ajax_refresh_time'} * 60000));
            {right_curly}

    {r});
{/if}

</script>
{/if}
	
{/foreach}


{if (!PHPFOX_IS_AJAX && !defined('PHPFOX_IS_AD_IFRAME')) || $bBlockIdForAds}
    </div>
</div>
{/if}
	
{if Phpfox::getParam('ad.ad_ajax_refresh')}
	<script type="text/javascript">	
		setTimeout('$.ajaxCall(\'ad.update\', \'block_id={$iBlockId}\', \'GET\');', ({param var='ad.ad_ajax_refresh_time'} * 60000));
		function fixHeight(iId, iHeight)
		{l}
			$('#' + iId).height(iHeight + '');
		{r}
	</script>
{/if}
	
{plugin call='ad.template_block_display__end'}