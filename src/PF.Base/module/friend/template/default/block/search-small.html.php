<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="search-friend-component">
    {if $sInputType == 'single'}
    <input type="hidden" name="{$sInputName}" id="search_friend_single_input" {if !empty($sUserIds)}value="{$sUserIds}"{/if}>
    {/if}
    <span id="js_custom_search_friend_placement">
        {if !empty($aCurrentValues)}
            {foreach from=$aCurrentValues item=aCurrentValue}
            <span id="js_friend_search_row_{$aCurrentValue.user_id}" class="item-user-selected">
                <span class="item-name">{$aCurrentValue.full_name}</span>
                <a role="button" class="friend_search_remove" data-search-id="js_custom_search_friend" title="Remove"
                    onclick="$Core.searchFriendsInput.removeSelected(this, {$aCurrentValue.user_id});  return false;">
                    <i class="ico ico-close"></i>
                </a>
                {if $sInputType == 'multiple'}
                <input type="hidden" name="{$sInputName}[]" value="{$aCurrentValue.user_id}">
                {/if}
            </span>
            {/foreach}
        {/if}
    </span>
    <span id="js_custom_search_friend"></span>
</div>
<script type="text/javascript">
  $Core.searchFriendsParams =
  {l}
      'id': '#js_custom_search_friend',
      'placement': '#js_custom_search_friend_placement',
      'width': '100%',
      'max_search': 10,
      'input_name': '{$sInputName}',
      'default_value': '{_p var='search_friends_by_their_name'}',
      'input_type': '{$sInputType}',
      'single_input': '#search_friend_single_input'
  {r};
</script>