<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          phpFox
 * @package         Phpfox
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if !count($aSuggestions)}
<div class="extra_info">
    {_p var='we_are_unable_to_find_any_friends_to_suggest_at_this_time_once_we_do_you_will_be_notified_within_our_dashboard'}
</div>
{else}
<div class="main_break"></div>
<div class="item-container clearfix js_suggestion_wrapper user-listing" id="collection-suggestions">
    {foreach from=$aSuggestions name=suggestion item=aUser}
    <article class="user-item">
        {template file='user.block.rows_wide'}
    </article>
    {/foreach}
</div>
<div class="clear"></div>
{/if}
