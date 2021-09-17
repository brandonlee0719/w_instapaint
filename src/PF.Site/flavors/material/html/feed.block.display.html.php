<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if !$bIsHashTagPop && !PHPFOX_IS_AJAX && !empty($sIsHashTagSearch)}
  <h1 id="sHashTagValue">#{$sIsHashTagSearchValue|clean}</h1>
{/if}

{plugin call='feed.component_block_display_process_header'}
{if Phpfox::isUser() && !defined('FEED_LOAD_MORE_NEWS') && !defined('FEED_LOAD_NEW_FEEDS') && (!isset($bIsGroupMember) || $bIsGroupMember) && !(isset($aFeedCallback.disable_share) && $aFeedCallback.disable_share)}
  {template file='feed.block.form'}
{/if}
<div id="js_new_feed_update"></div>
{if isset($bForceFormOnly) && $bForceFormOnly}
{else}
  {if Phpfox::isUser() && !PHPFOX_IS_AJAX && $sCustomViewType === null && $bUseFeedForm}
    <div id="js_main_feed_holder">
    </div>
  {/if}
  {if Phpfox::isUser() && !defined('PHPFOX_IS_USER_PROFILE') && !PHPFOX_IS_AJAX && !defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getParam('feed.allow_choose_sort_on_feeds') && empty($aFeedCallback.disable_sort)}
    <div class="feed-sort-order">
      <div class="feed-sort-holder dropdown" data-action="feed_sort_holder_click">
      <a href="#" class="feed-sort-order-link" data-toggle="dropdown">{_p var='sort_by'} <span class="ico ico-caret-down"></span></a>
        <ul class="dropdown-menu dropdown-menu-right">
          <li><a href="#"{if !$iFeedUserSortOrder} class="active"{/if} rel="0">{_p var='top_stories'}</a></li>
          <li><a href="#"{if $iFeedUserSortOrder} class="active"{/if} rel="1">{_p var='most_recent'}</a></li>
        </ul>
      </div>
    </div>
  {/if}
  {if Phpfox::isModule('captcha') && Phpfox::getUserParam('captcha.captcha_on_comment')}
  {module name='captcha.form' sType='comment' captcha_popup=true}
  {/if}
  {if !PHPFOX_IS_AJAX && !defined('FEED_LOAD_NEW_FEEDS') && !defined('FEED_LOAD_MORE_NEWS') }
  <div id="feed"><a name="feed"></a></div>
  <div id="js_feed_content" class="js_feed_content">
    {if $sCustomViewType !== null}
    <h2>{$sCustomViewType}</h2>
    {/if}
  <div id="js_new_feed_comment"></div>
  {/if}
    {if isset($bStreamMode) && $bStreamMode}
      {foreach from=$aFeeds item=aFeed}
        {if isset($aFeed.sponsored_feed) || $aFeed.feed_id != $iSponsorFeedId}
        <div class="feed_stream" data-feed-url="{if (isset($aFeedCallback.module))}{url link='feed.stream' id=$aFeed.feed_id module=$aFeedCallback.module item_id=$aFeedCallback.item_id}{else}{url link='feed.stream' id=$aFeed.feed_id}{if isset($aFeed.sponsored_feed)}&sponsor=1{/if}{/if}"></div>
        {/if}
    {/foreach}
    {else}
      {if isset($bNoLoadFeedContent)}
      {else}
      {foreach from=$aFeeds name=iFeed item=aFeed}
      {if isset($aFeed.sponsored_feed) || $aFeed.feed_id != $iSponsorFeedId}
      {if isset($aFeed.feed_mini) && !isset($bHasRecentShow)}
      {if $bHasRecentShow = true}{/if}
      <div class="activity_recent_holder">
        <div class="activity_recent_title">
          {_p var='recent_activity'}
        </div>
        {/if}
        {if !isset($aFeed.feed_mini) && isset($bHasRecentShow)}
      </div>
      {unset var=$bHasRecentShow}
      {/if}

      <div class="js_feed_view_more_entry_holder">
        {template file='feed.block.entry'}
        {if isset($aFeed.more_feed_rows) && is_array($aFeed.more_feed_rows) && count($aFeed.more_feed_rows)}
        {foreach from=$aFeed.more_feed_rows item=aFeed}
        {if $bChildFeed = true}{/if}
        <div class="js_feed_view_more_entry" style="display:none;">
          {template file='feed.block.entry'}
        </div>
        {/foreach}
        {unset var=$bChildFeed}
        {/if}
      </div>
      {/if}
      {/foreach}
    {/if}
  {/if}

    {if isset($bHasRecentShow) && !PHPFOX_IS_AJAX && !defined('FEED_LOAD_NEW_FEEDS') && !defined('FEED_LOAD_MORE_NEWS')}
  </div>
{/if}


{if $sCustomViewType === null && !defined('FEED_LOAD_NEW_FEEDS')}
  {if defined('PHPFOX_IN_DESIGN_MODE')}
{else}
{if count($aFeeds) || (isset($bForceReloadOnPage) && $bForceReloadOnPage)}
  { if !(defined('FEED_LOAD_NEW_NEWS') && FEED_LOAD_NEW_NEWS) }
  <div id="feed_view_more">
    {if $bIsHashTagPop}
    {if count($aFeeds) > 8}
    <a href="{url link='hashtag'}{$sIsHashTagSearch}/page_1/" class="global_view_more no_ajax_link btn btn-primary btn-round btn-gradient" style="display:block;">{_p var='load_more'}</a>
    {/if}
    {else}
    <div id="js_feed_pass_info" style="display:none;">page={$iFeedNextPage}{if defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id)}&profile_user_id={if !empty($mOnOtherUserProfile)}{$mOnOtherUserProfile}{else}{$aUser.user_id}{/if}{/if}{if isset($aFeedCallback.module)}&callback_module_id={$aFeedCallback.module}&callback_item_id={$aFeedCallback.item_id}{/if}&year={$sTimelineYear}&month={$sTimelineMonth}{if !empty($sIsHashTagSearch)}&hashtagsearch={$sIsHashTagSearch}{/if}</div>
    <div id="feed_view_more_loader"><i class="fa fa-spin fa-circle-o-notch" aria-hidden="true"></i></div>
    <a {if !PHPFOX_IS_AJAX && !defined('FEED_LOAD_NEW_FEEDS') && !defined('FEED_LOAD_MORE_NEWS') && isset($bForceReloadOnPage) && $bForceReloadOnPage} style="text-indent:-1000px; overflow:hidden; background:transparent; border:0px;"{/if} href="{if Phpfox_Module::instance()->getFullControllerName() == 'core.index-visitor'}{url link='core.index-visitor' page=$iFeedNextPage}{else}{url link='current' page=$iFeedNextPage}{/if}" onclick="$(this).remove(); $('#feed_view_more_loader').show();var oLastFeed = $('.js_parent_feed_entry').last();var iLastFeedId = (oLastFeed) ? oLastFeed.attr('id') : null; $.ajaxCall('feed.viewMore', '{if !empty($bForceFlavor)}force-flavor=material&{/if}page={$iFeedNextPage}{if defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id)}&profile_user_id={if !empty($mOnOtherUserProfile)}{$mOnOtherUserProfile}{else}{$aUser.user_id}{/if}{/if}{if isset($aFeedCallback.module)}&callback_module_id={$aFeedCallback.module}&callback_item_id={$aFeedCallback.item_id}{/if}&year={$sTimelineYear}&month={$sTimelineMonth}{if !empty($sIsHashTagSearch)}&hashtagsearch={$sIsHashTagSearch}{/if}&last-feed-id='+iLastFeedId, 'GET'); return false;" class="btn btn-primary btn-round btn-gradient global_view_more no_ajax_link">{_p var='load_more'}</a>

    {/if}
  </div>
  {/if}
{else}
  {if defined('PHPFOX_IS_USER_PROFILE') && Profile_Service_Profile::instance()->timeline()}
  {module name='user.birth'}
  {else}
  {if !defined('FEED_LOAD_NEW_FEEDS') }
  <div class="message js_no_feed_to_show">{_p var='there_are_no_new_feeds_to_view_at_this_time'}</div>
  {/if}
  {/if}
{/if}
{/if}
{/if}
  {if !PHPFOX_IS_AJAX || (PHPFOX_IS_AJAX && count($aFeedVals))}
  </div>
  {/if}

  {if Phpfox::getParam('feed.refresh_activity_feed') > 0}
  <script type="text/javascript">
    window.$iCheckForNewFeedsTime = {param var="feed.refresh_activity_feed"};
  </script>
  {/if}
{/if}

<script type="text/javascript">
  $Behavior.hideEmptyActionList = function() {l}
    $('[data-component="feed-options"] ul.dropdown-menu').each(function() {l}
      if ($(this).find('li').length == 0) {l}
        oParent = $(this).parent('[data-component="feed-options"]');
        if (oParent) {l}
            oParent.find('a:first').hide();
        {r}
      {r}
    {r});
  {r};
</script>