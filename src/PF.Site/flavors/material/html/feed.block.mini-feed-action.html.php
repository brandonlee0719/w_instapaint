<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="js_feed_comment_border">
    {plugin call='feed.template_block_mini_feed_action_border'}
    <div id="js_feed_mini_action_holder_{if isset($aFeed.like_type_id) && !isset($aFeed.is_app)}{$aFeed.like_type_id}{else}{$aFeed.type_id}{/if}_{if isset($aFeed.like_item_id) && !isset($aFeed.is_app)}{$aFeed.like_item_id}{else}{$aFeed.item_id}{/if}" class="comment_mini_content_holder{if (isset($aFeed.is_app) && $aFeed.is_app && isset($aFeed.app_object))} _is_app{/if}"{if (isset($aFeed.is_app) && $aFeed.is_app && isset($aFeed.app_object))} data-app-id="{$aFeed.app_object}"{/if}>
            <div class="comment_mini_content_holder_icon"{if isset($aFeed.marks) || (isset($aFeed.likes) && is_array($aFeed.likes)) || (isset($aFeed.total_comment) && $aFeed.total_comment > 0)}{else}{/if}></div>
                <div class="comment_mini_content_border">

                    <div class="comment-mini-content-commands">
                        <div class="button-like-share-block {if isset($aFeed.total_action)}comment-has-{$aFeed.total_action}-actions{/if}">
                            {if $aFeed.can_like}
                            <div class="feed-like-link">
                                {if isset($aFeed.like_item_id)}
                                {module name='like.link' like_type_id=$aFeed.like_type_id like_item_id=$aFeed.like_item_id like_is_liked=$aFeed.feed_is_liked}
                                {else}
                                {module name='like.link' like_type_id=$aFeed.like_type_id like_item_id=$aFeed.item_id like_is_liked=$aFeed.feed_is_liked}
                                {/if}

                                <span class="counter" onclick="return $Core.box('like.browse', 450, 'type_id={if isset($aFeed.like_type_id)}{$aFeed.like_type_id}{else}{$aFeed.type_id}{/if}&amp;item_id={$aFeed.item_id}');">{if !empty($aFeed.feed_total_like)}{$aFeed.feed_total_like}{/if}</span>

                            </div>
                            {/if}
                            {if (!isset($sFeedType) ||  $sFeedType != 'mini') && $aFeed.can_comment}
                                <div class="feed-comment-link">
                                    <a href="#" onclick="$('#js_feed_comment_form_textarea_{$aFeed.feed_id}').focus();return false;"><span class="ico ico-comment-o"></span></a>
                                    <span class="counter">{if !empty($aFeed.total_comment)}{$aFeed.total_comment}{/if}</span>
                                </div>
                            {/if}
                            {if $aFeed.can_share}
                            <div class="feed-comment-share-holder">
                                {assign var=empty value=false}
                                {if $aFeed.privacy == '0' || $aFeed.privacy == '1' || $aFeed.privacy == '2'}
                                    {if isset($aFeed.share_type_id)}
                                {module name='share.link' type='feed' display='menu_btn' url=$aFeed.feed_link title=$aFeed.feed_title sharefeedid=$aFeed.item_id sharemodule=$aFeed.share_type_id}
                                    {else}
                                        {module name='share.link' type='feed' display='menu_btn' url=$aFeed.feed_link title=$aFeed.feed_title sharefeedid=$aFeed.item_id sharemodule=$aFeed.type_id}
                                    {/if}
                                {else}
                                {module name='share.link' type='feed' display='menu_btn' url=$aFeed.feed_link title=$aFeed.feed_title}
                                {/if}
                                <span class="counter">{if !empty($aFeed.total_share)}{$aFeed.total_share}{/if}</span>
                            </div>
                            {/if}
                        </div>


                        {plugin call='feed.template_block_mini_feed_action_commands_1'}

                    </div>
                </div><!-- // .comment_mini_content_border -->
    </div><!-- // .comment_mini_content_holder -->
</div>

<script type="text/javascript">
	{literal}
	$Behavior.hideEmptyFeedOptions = function() {
		$('[data-component="feed-options"] ul.dropdown-menu').each(function() {
			if ($(this).children().length == 0) {
				$(this).closest('[data-component="feed-options"]').hide();
			}
		});
	}
	{/literal}
</script>