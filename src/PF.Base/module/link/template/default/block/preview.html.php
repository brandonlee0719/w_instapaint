<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: preview.html.php 3533 2011-11-21 14:07:21Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if isset($aLink.images) && $iTotalImages = (int) count($aLink.images)}{/if}
<script type="text/javascript">
	var $iTotalAttachmentImages = {if isset($iTotalImages)}{$iTotalImages}{else}0{/if};	
	$Core.loadStaticFile('{jscript file='preview.js' module='link'}');
</script>
<input type="hidden" name="val[link][url]" value="{$aLink.link|clean}">
{if !empty($aLink.embed_code)}
<div style="display:none;"><textarea cols="30" rows="4" name="val[link][embed_code]">{$aLink.embed_code|clean}</textarea></div>
{/if}
<div class="feed_link_content_preview">
    <div class="activity_feed_content_link">
        {if !empty($aLink.default_image)}
            <div class="activity_feed_content_image">
                <div class="attachment_image_holder">
                        <input type="hidden" name="val[link][image_hide]" value="0" id="js_attachment_link_default_image_hide" />
                        <input type="hidden" name="val[link][image]" value="{$aLink.default_image}" id="js_attachment_link_default_image_input" />
                        <div id="js_attachment_link_default_image">
                            <img src="{$aLink.default_image}" alt=""/>
                        </div>
                </div>
            </div>
        {/if}
        <div class="feed_block_title_content activity_feed_content_float">
            {if isset($aLink.title)}
                <div class="js_text_attachment_edit" style="display:none;">
                    <input type="text" name="val[link][title]" value="{$aLink.title|clean}" class="js_text_attachment_edit_value" />
                </div>
                <a class="activity_feed_content_link_title js_text_attachment_edit_link" href="#">{$aLink.title|clean}</a>
            {/if}
            <div class="activity_feed_content_link_title_link">
                {$aLink.host|clean}
            </div>
            {if isset($aLink.description)}
            <div class="activity_feed_content_display">
                <div class="js_text_attachment_edit" style="display:none;">
                    <textarea cols="30" rows="4" name="val[link][description]" class="js_text_attachment_edit_value">{$aLink.description|clean}</textarea>
                </div>
                <div class="activity_feed_content_display_info js_text_attachment_edit_link">{$aLink.description|feed_strip|stripbb|split:55}</div>
            </div>
            {/if}
            {if isset($aLink.images)}
                {if $iTotalImages > 1}
                    <div style="display:none;">
                        {foreach from=$aLink.images name=images item=sImage}
                            <div id="js_hidden_attachment_image_value_{$phpfox.iteration.images}">{$sImage}</div>
                            <div id="js_hidden_attachment_image_{$phpfox.iteration.images}">
                                <img src="{$sImage}" alt="" style="max-width:120px;" />
                            </div>
                        {/foreach}
                    </div>
                    <div class="attachment_pager">
                        <ul>
                            <li class="no_link"><a href="#" onclick="return $Core.changeDefaultAttachmentImage(this, 'previous');" class="previous first">{_p var='previous'}</a></li>
                            <li><a href="#" onclick="return $Core.changeDefaultAttachmentImage(this, 'next');" class="next">{_p var='next'}</a></li>
                            <li class="counter"><span id="js_attachment_link_counter">1 of {$iTotalImages}</span><span class="small">{_p var='choose_a_thumbnail'}</span></li>
                        </ul>
                        <div class="clear"></div>
                    </div>
                {/if}
                <div>
                    <label><input type="checkbox" name="attachment_link_checkbox" value="0" onchange="$Core.toggleAttachmentLinkThumb(this);" > {_p var='no_thumbnail'}</label>
                </div>
            {/if}
        </div>
    </div>
</div>