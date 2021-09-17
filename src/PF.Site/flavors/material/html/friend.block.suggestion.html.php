<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if !PHPFOX_IS_AJAX}
<div id="js_friend_suggestion_loader" style="display:none;"><i class="fa fa-spinner fa-spin"></i> {_p var='finding_another_suggestion'}</div>
<div id="js_friend_suggestion">
{/if}
{if isset($aSuggestion.user_id)}
    <div class="user_rows_mini core-friend-block">
        <div class="user_rows">
            <div class="user_rows_image">
                {img user=$aSuggestion suffix='_120_square'}
            </div>
            <div class="user_rows_inner">
                {$aSuggestion|user}
                {assign var=aUser value=$aSuggestion}
                {module name='user.friendship' friend_user_id=$aSuggestion.user_id type='icon' extra_info=true mutual_list=true}
            </div>
            <a class="item-hide" role="button" onclick="$('#js_friend_suggestion').hide(); $('#js_friend_suggestion_loader').show(); $.ajaxCall('friend.removeSuggestion', 'user_id={$aSuggestion.user_id}&amp;load=true'); return false;" title="{_p var='hide_this_suggestion'}"><span class="ico ico-close"></span></a>
        </div>
    </div>
{/if}
{if !PHPFOX_IS_AJAX}
</div>
{/if}
