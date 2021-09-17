<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="feed-table-tagging">
    <div class="js_feed_compose_extra feed_compose_extra js_feed_compose_tagging dont-unbind-children" style="display: none;">
        <div class="feed-box">
            <div class="feed-with">{_p('With')}</div>
            <div class="feed-tagging-input-box">
                <input type="hidden" data-feedid = "{if isset($iFeedId)}{$iFeedId}{else}0{/if}" id="feed_input_tagged{if isset($iFeedId)}_{$iFeedId}{else}_0{/if}" name="val[tagged_friends]" value="{if isset($aForms.tagged_friends)}{$aForms.tagged_friends}{/if}">
                <span class="js_feed_tagged_items feed_tagged_items"></span>
                <span class="js_feed_input_tagging_wrapper feed_input_tagging_wrapper">
                    <input type="text" class="js_input_tagging" placeholder="{_p('who_is_with_you')}">
                </span>
            </div>
        </div>
    </div>
</div>
